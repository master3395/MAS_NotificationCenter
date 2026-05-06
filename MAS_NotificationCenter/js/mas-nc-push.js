(function () {
  'use strict';

  function urlBase64ToUint8Array(base64String) {
    var padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);
    for (var i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
  }

  function ready(fn) {
    if (document.readyState !== 'loading') {
      fn();
    } else {
      document.addEventListener('DOMContentLoaded', fn);
    }
  }

  ready(function () {
    var cfg = document.getElementById('mas-nc-push-config');
    var btn = document.getElementById('mas-nc-subscribe-btn');
    if (!cfg || !btn) {
      return;
    }
    var subscribeUrl = cfg.getAttribute('data-subscribe-url');
    var vapid = cfg.getAttribute('data-vapid-public');
    var swUrl = cfg.getAttribute('data-sw-url') || '/mas_nc_sw.js';
    if (!subscribeUrl || !vapid) {
      btn.disabled = true;
      return;
    }
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
      btn.disabled = true;
      return;
    }

    btn.addEventListener('click', function () {
      navigator.serviceWorker.register(swUrl).then(function (reg) {
        return reg.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: urlBase64ToUint8Array(vapid)
        });
      }).then(function (sub) {
        var json = typeof sub.toJSON === 'function' ? sub.toJSON() : JSON.parse(JSON.stringify(sub));
        return fetch(subscribeUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'same-origin',
          body: JSON.stringify(json)
        }).then(function (res) {
          return res.json();
        });
      }).then(function (data) {
        if (data && data.ok) {
          btn.textContent = 'Subscribed';
        } else {
          btn.textContent = 'Subscribe failed';
        }
      }).catch(function () {
        btn.textContent = 'Subscribe failed';
      });
    });
  });
})();
