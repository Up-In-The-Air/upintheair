$(document).ready(function() {
  $('#input-area').emoji({
    showTab: true,
    animation: 'fade',
    icons: [{
      name: 'Baidu',
      path: "/static/images/emoji/tieba/",
      maxNum: 50,
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
        name: "QQ",
        path: "/static/images/emoji/qq/",
        maxNum: 91,
        file: ".gif",
        placeholder: "#qq_{alias}#"
    }]
  });

  $('.emoji_btn').click(function() {
    $('.emoji_container').animate({ top: 281 });
  });
});
