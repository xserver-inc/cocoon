const { DefinePlugin } = require( 'webpack' );
const { CleanWebpackPlugin } = require( 'clean-webpack-plugin' );
const MiniCSSExtractPlugin = require( 'mini-css-extract-plugin' );
const { BundleAnalyzerPlugin } = require( 'webpack-bundle-analyzer' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

const isProduction = process.env.NODE_ENV === 'production';

// ビルド中のCPU占有を抑える圧縮処理の並列数制限
for ( const minimizer of defaultConfig.optimization?.minimizer || [] ) {
  if ( minimizer.options?.parallel === true || minimizer.options?.parallel > 2 ) {
    minimizer.options.parallel = 2;
  }
}

module.exports = {
  ...defaultConfig,
  parallelism: 2,
  entry: {
    'blocks.build': path.resolve( './src', 'blocks.js' ),
    'legacy-jsx-runtime': path.resolve( './src', 'legacy-jsx-runtime.js' ),
  },
  output: {
    path: path.join( __dirname, './dist' ),
    filename: '[name].js',
  },
  plugins: [
    new DefinePlugin( {
      SCRIPT_DEBUG: ! isProduction,
    } ),
    new CleanWebpackPlugin( {
      cleanAfterEveryBuildPatterns: [ '!fonts/**', '!images/**' ],
      cleanStaleWebpackAssets: false,
    } ),
    process.env.WP_BUNDLE_ANALYZER && new BundleAnalyzerPlugin(),
    new MiniCSSExtractPlugin( { filename: '[name].css' } ),
    ! process.env.WP_NO_EXTERNALS && new DependencyExtractionWebpackPlugin(),
  ].filter( Boolean ),
};
