fis.match('::package', {
  spriter: fis.plugin('csssprites')
});

fis.match('*.html', {
  release: '$0'
});

fis.match('static/**.{js,less,png}', {
  useHash: true
});

fis.match('static/js/**.js', {
  preprocessor: fis.plugin('browserify'),
  release: '$0'
});

fis3.match('static/js/test.es', {
    parser: fis.plugin('babel'),
    release: '$0',
    rExt: '.js'
})

fis.match('static/js/**.js', {
  optimizer: fis.plugin('uglify-js'),
  release: '$0'
});

fis.match('static/css/(*.less)', {
  parser: fis.plugin('less'),
  rExt: '.css',
  useSprite: true,
  optimizer: fis.plugin('clean-css'),
  release: 'static/css/$1'
});

fis.match('static/css/lib/*.css', {
  useSprite: true,
  optimizer: fis.plugin('clean-css'),
  release: '$0'
});

fis.match('static/images/(*.{png,ico})', {
  optimizer: fis.plugin('png-compressor'),
  release: 'static/images/$1'
});

fis.media('debug').match('**.{js,less,png}', {
  useHash: false,
  useSprite: false,
  optimizer: null
});
