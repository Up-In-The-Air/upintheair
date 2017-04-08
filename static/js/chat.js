// For Debug Mode
Vue.config.devtools = true;

// Change to localhost when debugging locally
var WEB_SOCKET_HOST = 'ws://192.17.90.133:12345';
// var WEB_SOCKET_HOST = 'ws://localhost:12345';
var OPTIONS = {
  icons: [{
    path: "/static/images/emoji/tieba/",
    file: ".jpg",
    placeholder: ":{alias}:",
    alias: {
      1: "smile",
      2: "lol",
      3: "naughty",
      4: "shocked",
      5: "cool",
      6: "angry",
      7: "happy",
      8: "perspiration",
      9: "cry",
      10: "embarrassed",
      11: "disdain",
      12: "unhappy",
      13: "nicejob",
      14: "money",
      15: "doubt",
      16: "yinxian",
      17: "tu",
      18: "yi",
      19: "weiqu",
      20: "huaxin",
      21: "hu",
      22: "xiaonian",
      23: "neng",
      24: "taikaixin",
      25: "huaji",
      26: "mianqiang",
      27: "kuanghan",
      28: "guai",
      29: "shuijiao",
      30: "jinku",
      31: "shengqi",
      32: "jinya",
      33: "pen",
      34: "aixin",
      35: "xinsui",
      36: "meigui",
      37: "liwu",
      38: "caihong",
      39: "xxyl",
      40: "taiyang",
      41: "qianbi",
      42: "dnegpao",
      43: "chabei",
      44: "dangao",
      45: "yinyue",
      46: "haha2",
      47: "shenli",
      48: "damuzhi",
      49: "ruo",
      50: "OK"
    }
  }, {
      path: "/static/images/emoji/qq/",
      file: ".gif",
      placeholder: "#qq_{alias}#"
  }]
};

var app = new Vue({
  el: '#app',
  data: function() {
    return {
      user: {},
      userList: {},
      contactList: [],
      newMessageList: [],
      socket: null,
      messageObjList: [],
      receiver: {},
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
          _this.getUserList();
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
          var messageObj = JSON.parse(msg.data);
          if (_this.receiver.id == messageObj.sender_id) {
            _this.log(messageObj.message, false);
          } else {
            var alreadyInContact = false;
            for (var i = 0; i < _this.contactList.length; i++) {
              if (_this.contactList[i].id == messageObj.sender_id) {
                _this.newMessageList.splice(i, 1, _this.newMessageList[i] + 1);
                alreadyInContact = true;
                break;
              }
            }
            if (!alreadyInContact) {
              for (var name in _this.userList) {
                if (_this.userList.hasOwnProperty(name) && _this.userList[name] == messageObj.sender_id) {
                  _this.contactList.unshift({
                    id: _this.userList[name],
                    first_name: name.split(' ')[0],
                    last_name: name.split(' ')[1]
                  });
                  _this.newMessageList.unshift(1);
                }
              }
            }
          }
        };
      } catch (ex) {
        Materialize.toast('Fail to connect to this user!', 4000);
      }
      // Send configuration (send own user_id)
      setTimeout(function() {
        _this.send(null, '[INITIALIZATION_CONFIG]');
      }, 1000);
    },
    send: function(receiverId, msg) {
      try {
        this.socket.send(JSON.stringify({
          user_id: this.user.id,
          receiver_id: receiverId,
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
      Vue.nextTick(function() {
        // Render emoji and scroll to bottom
        $('.message-content').emojiParse(OPTIONS);
        $(".message-list-pane").animate({ scrollTop: $('.message-list-pane').prop("scrollHeight")}, 1000);
      });
    },
    onSendClick: function() {
      if (!this.message) {
        return;
      }
      this.send(this.receiver.id, this.message);
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
            _this.onSelectReceiver(_this.contactList[0], 0);
            _this.newMessageList = [];
            _this.contactList.forEach(function(contact) {
              _this.newMessageList.push(0);
            })
          }
        }
      });
    },
    getUserList: function() {
      var _this = this;
      $.ajax({
        method: 'GET',
        url: 'api/get_user_list.php',
        data: { user_id: this.user.id },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          var autocompleteData = {};
          resp.data.forEach(function(user) {
            var name = user.first_name + ' ' + user.last_name;
            autocompleteData[name] = null;
            _this.userList[name] = user.id;
          });
          $('input#search').autocomplete({
            data: autocompleteData,
            limit: 10,
            onAutocomplete: function(val) {
              var alreadyInContact = false;
              for (var i = 0; i < _this.contactList.length; i++) {
                if (_this.contactList[i].id == _this.userList[val]) {
                  _this.onSelectReceiver(_this.contactList[i], i);
                  alreadyInContact = true;
                  break;
                }
              }
              if (!alreadyInContact) {
                _this.contactList.unshift({
                  id: _this.userList[val],
                  first_name: val.split(' ')[0],
                  last_name: val.split(' ')[1]
                });
                _this.newMessageList.unshift(0);
                _this.receiver = {
                  id: _this.userList[val],
                  firstName: val.split(' ')[0],
                  lastName: val.split(' ')[1]
                };
                _this.messageObjList = [];
              }
            }
          });
        }
      });
    },
    onSelectReceiver: function(receiver, index) {
      this.receiver = {
        id: receiver.id,
        firstName: receiver.first_name,
        lastName: receiver.last_name
      };
      this.newMessageList.splice(index, 1, 0);
      this.messageObjList = [];
      this.getChatHistory(this.receiver.id, 10);
    },
    getChatHistory: function(receiverId, limit) {
      var _this = this;
      var upstreamData = {
        user_id: this.user.id,
        receiver_id: receiverId
      };
      if (limit) {
        upstreamData.limit = limit;
      }
      $.ajax({
        method: 'GET',
        url: 'api/get_chat_history.php',
        data: upstreamData,
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          var tempList = resp.data.reverse();
          tempList.forEach(function(item) {
            _this.messageObjList.push({
              send: item.sender_id == _this.user.id,
              content: item.message
            });
          });
          Vue.nextTick(function() {
            // Render emoji and scroll to bottom
            $('.message-content').emojiParse(OPTIONS);
            $(".message-list-pane").animate({ scrollTop: $('.message-list-pane').prop("scrollHeight")}, 1000);
          });
        }
      });
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
