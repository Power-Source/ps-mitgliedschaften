const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const origin = 'https://members.example';
const assets = ['ms-public-ajax.js', 'ms-public-ajax.min.js'];
const adminAssets = ['ms-admin.js', 'ms-admin.min.js'];

function loadAdminAsset(asset, targetUrl = '', membershipIds = []) {
  const elements = new Map();
  const location = { origin, href: origin + '/wp-admin/admin.php' };
  let confirmation;
  function element(selector) {
    if (!elements.has(selector)) {
      const value = {
        handlers: {},
        classes: new Set(),
        ready() { return this; },
        on(event, handler) { this.handlers[event] = handler; return this; },
        find() { return element('empty'); },
        parents() { return element('empty'); },
        addClass(name) { this.classes.add(name); return this; },
        val() { return targetUrl; },
        attr() { return targetUrl; },
        each(callback) {
          if (selector === 'input.del_membership_ids:checked') {
            membershipIds.forEach(id => callback.call({ val() { return id; } }));
          }
          return this;
        },
      };
      elements.set(selector, value);
    }
    return elements.get(selector);
  }
  function jQuery(selector) {
    if (typeof selector === 'function') {
      return;
    }
    return selector && typeof selector === 'object' && selector.val
      ? selector : element(selector);
  }
  jQuery.fn = {};
  const window = { location };
  const context = vm.createContext({
    URL,
    window,
    document: {},
    jQuery,
    ms_data: { lang: { msg_bulk_delete: 'Delete?', btn_delete: 'Delete', btn_cancel: 'Cancel' } },
    wpmUi: { confirm(args) { confirmation = args; } },
  });
  const filename = path.join(__dirname, '../app/assets/js', asset);
  vm.runInContext(fs.readFileSync(filename, 'utf8'), context, { filename });
  return {
    sanitize: context.sanitizeRedirectUrl,
    location,
    bulkDelete(key = 0) {
      window.ms_init.bulk_delete_membership();
      const button = element('.bulk_delete_memberships_button');
      button.handlers.click.call(button, { preventDefault() {} });
      confirmation.callback(key);
    },
    selectSite() {
      window.ms_init.memberships_column = function() {};
      window.ms_init.view_protected_content();
      const select = element('#select-site');
      select.handlers.change();
      return select.classes.has('wpmui-loading');
    },
  };
}

function loadAsset(asset, redirectValue = '') {
  if (adminAssets.includes(asset)) {
    return loadAdminAsset(asset, redirectValue);
  }
  const handlers = {};
  const location = {
    origin,
    href: origin + '/login',
    reloads: 0,
    reload() { this.reloads += 1; },
  };
  let request;
  const form = {
    addClass() { return this; },
    removeClass() { return this; },
    show() { return this; },
    text() { return this; },
    prop() { return this; },
    each() { return this; },
    find() { return this; },
    val() { return redirectValue; },
    serializeArray() { return []; },
    hasClass() { return false; },
    on(event, handler) {
      handlers[event] = handler;
      return this;
    },
  };
  const other = Object.create(form);
  other.on = function() { return this; };
  function jQuery(selector) {
    if (typeof selector === 'function') {
      selector();
      return;
    }
    return selector === 'form[action="login"]' || selector === form ? form : other;
  }
  jQuery.ajax = function(options) { request = options; };
  const context = vm.createContext({
    URL,
    window: { location, ms_ajax_login: { ajaxurl: '/admin-ajax.php' } },
    document: { location },
    jQuery,
  });
  const filename = path.join(__dirname, '../app/assets/js', asset);
  vm.runInContext(fs.readFileSync(filename, 'utf8'), context, { filename });

  return {
    sanitize: context.sanitizeRedirectUrl,
    location,
    login(response) {
      handlers.submit.call(form, { preventDefault() {} });
      request.success(response);
    },
  };
}

for (const asset of [...assets, ...adminAssets]) {
  test(`${asset}: preserves same-origin redirects without reinterpreting paths`, () => {
    const { sanitize } = loadAsset(asset);
    const cases = [
      ['/account?tab=plan#details', origin + '/account?tab=plan#details'],
      [origin + '/account', origin + '/account'],
      ['//members.example/account', origin + '/account'],
      [origin + '//evil.example/account', origin + '//evil.example/account'],
      ['/.//evil.example/account', origin + '//evil.example/account'],
      ['/a/..//evil.example', origin + '//evil.example'],
      ['/account?next=%2Fbilling&tab=plan#details', origin + '/account?next=%2Fbilling&tab=plan#details'],
      ['/<script>alert(1)</script>', origin + '/%3Cscript%3Ealert(1)%3C/script%3E'],
    ];
    for (const [input, expected] of cases) {
      assert.equal(sanitize(input), expected, input);
      assert.equal(new URL(sanitize(input), origin).origin, origin, input);
    }
  });

  test(`${asset}: rejects unsafe or unsupported redirects`, () => {
    const { sanitize } = loadAsset(asset);
    const inputs = [
      undefined, null, false, 1, {}, [], '',
      'account', '?next=account', '#account',
      'javascript:alert(document.domain)',
      'data:text/html,<script>alert(1)</script>',
      'vbscript:msgbox(1)', 'file:///etc/passwd',
      'https://evil.example/account', 'http://members.example/account',
      'https://members.example:8443/account',
      'https://members.example.evil.example/account',
      'https://members.example@evil.example/account',
      '//evil.example/account', '///evil.example/account',
      '/\\evil.example/account', '/\n/evil.example/account',
      'https://[invalid',
    ];
    for (const input of inputs) {
      assert.equal(sanitize(input), null, String(input));
    }
  });

}

for (const asset of assets) {
  test(`${asset}: login uses the validated server redirect`, () => {
    const app = loadAsset(asset, '/fallback');
    app.login({ loggedin: true, redirect: origin + '//evil.example/account' });
    assert.equal(app.location.href, origin + '//evil.example/account');
    assert.equal(app.location.reloads, 0);
  });

  test(`${asset}: login validates the form redirect fallback`, () => {
    const app = loadAsset(asset, '/account?tab=plan#details');
    app.login({ loggedin: true });
    assert.equal(app.location.href, origin + '/account?tab=plan#details');
    assert.equal(app.location.reloads, 0);
  });

  test(`${asset}: invalid redirects reload rather than navigate`, () => {
    for (const response of [
      { loggedin: true, redirect: '//evil.example/account' },
      { loggedin: true, redirect: 'javascript:alert(1)' },
      { loggedin: true },
    ]) {
      const app = loadAsset(asset, '/\\evil.example/account');
      app.login(response);
      assert.equal(app.location.href, origin + '/login');
      assert.equal(app.location.reloads, 1);
    }
  });

  test(`${asset}: malformed response redirects use the form fallback`, () => {
    for (const redirect of [null, {}, [], 42]) {
      const app = loadAsset(asset, '/account');
      app.login({ loggedin: true, redirect });
      assert.equal(app.location.href, origin + '/account');
    }
  });

  test(`${asset}: unsuccessful login does not navigate or reload`, () => {
    const app = loadAsset(asset, '/account');
    app.login({ loggedin: false, error: 'Login failed' });
    assert.equal(app.location.href, origin + '/login');
    assert.equal(app.location.reloads, 0);
  });
}

for (const asset of adminAssets) {
  test(`${asset}: bulk deletion preserves membership IDs and admin query parameters`, () => {
    const app = loadAdminAsset(asset, '/wp-admin/admin.php?page=memberships&action=delete&_wpnonce=abc', ['12', '34']);
    app.bulkDelete();
    assert.equal(app.location.href, origin + '/wp-admin/admin.php?page=memberships&action=delete&_wpnonce=abc&membership_ids=12-34');
  });

  test(`${asset}: cancelling bulk deletion does not navigate`, () => {
    const app = loadAdminAsset(asset, '/wp-admin/admin.php?action=delete');
    app.bulkDelete(1);
    assert.equal(app.location.href, origin + '/wp-admin/admin.php');
  });

  test(`${asset}: both admin handlers keep double-slash paths on the current origin`, () => {
    for (const action of ['bulkDelete', 'selectSite']) {
      const app = loadAdminAsset(asset, origin + '//evil.example/account');
      app[action]();
      assert.equal(app.location.href, origin + '//evil.example/account');
    }
  });

  test(`${asset}: site switching preserves the URL and sets the loading state`, () => {
    const app = loadAdminAsset(asset, '/wp-admin/admin.php?page=protection&site=2#rules');
    assert.equal(app.selectSite(), true);
    assert.equal(app.location.href, origin + '/wp-admin/admin.php?page=protection&site=2#rules');
  });

  test(`${asset}: both admin handlers block external URLs and executable schemes`, () => {
    for (const target of ['//evil.example/account', '/\\evil.example/account', 'javascript:alert(1)', 'https://evil.example/account']) {
      for (const action of ['bulkDelete', 'selectSite']) {
        const app = loadAdminAsset(asset, target);
        const loading = app[action]();
        assert.equal(app.location.href, origin + '/wp-admin/admin.php');
        if (action === 'selectSite') {
          assert.equal(loading, false);
        }
      }
    }
  });
}
