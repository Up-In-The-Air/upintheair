// For Debug Mode
Vue.config.devtools = true;

// Change to localhost when debugging locally
var WEB_SOCKET_HOST = 'ws://localhost:12345';

var app = new Vue({
  el: '#app',
  data: function() {
    return {
      user: {},
      contactList: [],
      socket: null,
      messageObjList: [],
      receiver: null,
      message: ''
    }
  },
  ready: function() {
    // Auto login
    var cookies = document.cookie.split('; ');
    var cookieObj = {};
    cookies.forEach(function(cookieStr) {
      var cookieName = cookieStr.split('=')[0];
      var cookieContent = cookieStr.split('=')[1];
      cookieObj[cookieName] = cookieName === 'upintheairAuth' ? JSON.parse(cookieContent) : cookieContent;
    });

    var _this = this;
    if (cookieObj.hasOwnProperty('upintheairAuth')) {
      $.ajax({
        method: 'POST',
        url: 'api/auto_login.php',
        data: cookieObj['upintheairAuth'],
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            location.href = '/';
            return;
          }
          _this.user = {
            firstName: resp.data.first_name,
            lastName: resp.data.last_name,
            email: resp.data.email,
            id: resp.data.id
          };

          _this.getInitContactList();
        },
        error: function() { location.href = '/'; }
      });
    }

    this.initializeWebSocket();

  },
  methods: {
    initializeWebSocket: function() {
      var _this = this;

      try {
        this.socket = new WebSocket(WEB_SOCKET_HOST);
        this.socket.onopen = function(msg) {};
        this.socket.onclose = function(msg) {};
        this.socket.onmessage = function(msg) {
          _this.log(msg.data, false);
        };
      } catch (ex) {
        Materialize.toast('Fail to connect to this user!', 4000);
      }
      // Send configuration (send own user_id)
      setTimeout(function() {
        _this.send(null, '[INITIALIZATION_CONFIG]');
      }, 1000);
    },
    send: function(receiver, msg) {
      try {
        this.socket.send(JSON.stringify({
          user_id: this.user.id,
          receiver_id: receiver,
          msg: msg
        }));
      } catch (ex) {
        Materialize.toast('Fail to send message!', 4000);
      }
    },
    log: function(msg, send) {
      this.messageObjList.push({
        send: send,
        content: msg
      });
    },
    onSendClick: function() {
      if (!this.message) {
        return;
      }
      this.send(this.receiver, this.message);
      this.log(this.message, true);
      this.message = '';
    },
    getInitContactList: function() {
      var _this = this;
      $.ajax({
        method: 'GET',
        url: 'api/get_contact_list.php',
        data: { user_id: this.user.id },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          _this.contactList = resp.data;
          if (_this.contactList.length > 0) {
            _this.receiver = _this.contactList[0].id;
          }
        }
      });
    },
    onSelectReceiver: function(receiverId) {
      this.receiver = receiverId;
    },
    onLogoutClick: function() {
      $.ajax({
        method: 'POST',
        url: 'api/log_out.php',
        data: { user_id: this.user.id },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          document.cookie = 'upintheairAuth=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
          location.href = '/';
        }
      });
    }
  }
});
