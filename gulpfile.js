// Gulp と各プラグインの読み込み
const gulp = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const rename = require('gulp-rename');
const plumber = require('gulp-plumber');

// style.css のみをビルドするタスク（バージョンアップ時に使用）
const scssBuildStyleOnly = (done) => {
    gulp.src('./scss/style.scss')
        .pipe(plumber())
        .pipe(sass({
            outputStyle: "expanded"
        }))
        .pipe(gulp.dest('./'));
    done();
}

// フロント用 CSS（style.css, keyframes.css, amp.css）をビルドするタスク
const scssBuildFront = (done) => {
    gulp.src(['./scss/style.scss', './scss/animation/keyframes.scss', './scss/amp.scss'])
        .pipe(plumber())
        .pipe(sass({
            outputStyle: "expanded"
        }))
        .pipe(gulp.dest('./'));
    done();
}

// Font Awesome 用 CSS をビルドするタスク
const scssBuildFontAwesome = (done) => {
    gulp.src('./scss/fontawesome5.scss')
        .pipe(plumber())
        .pipe(sass({
            outputStyle: "expanded"
        }))
        .pipe(gulp.dest('./css/'));
    done();
}

// 管理画面用 CSS をビルドするタスク
const scssBuildAdmin = (done) => {
    gulp.src('./scss/admin.scss')
        .pipe(plumber())
        .pipe(sass({
            outputStyle: "expanded"
        }))
        .pipe(gulp.dest('./css/'));
    done();
}

// エディター用 CSS をビルドしてテーマルートに出力するタスク
const scssBuildEditor = (done) => {
    gulp.src('./scss/_editor-style.scss')
        .pipe(plumber())
        .pipe(rename({basename: 'editor-style'}))
        .pipe(sass({
            outputStyle: "expanded"
        }))
        .pipe(gulp.dest('./'));
    done();
}

// エディター用 CSS をビルドして css/ フォルダに出力するタスク
const scssBuildEditorToCss = (done) => {
    gulp.src('./scss/_editor-style.scss')
        .pipe(plumber())
        .pipe(rename({basename: 'editor-style'}))
        .pipe(sass({
            outputStyle: "expanded"
        }))
        .pipe(gulp.dest('./css/'));
    done();
}

// Gutenberg ブロックエディター用 CSS をビルドするタスク
const scssBuildBlock = (done) => {
    gulp.src('./scss/gutenberg-editor.scss')
        .pipe(plumber())
        .pipe(sass({
            outputStyle: "expanded"
        }))
        .pipe(gulp.dest('./css/'));
    done();
}

// エディターページ用 CSS をビルドするタスク
const scssBuildEditorPage = (done) => {
    gulp.src('./scss/editor-page.scss')
        .pipe(plumber())
        .pipe(sass({
            outputStyle: "expanded"
        }))
        .pipe(gulp.dest('./css/'));
    done();
}

// 記事本文エリア用 CSS をビルドするタスク
const scssBuildEntryContent = (done) => {
    gulp.src('./scss/entry-content.scss')
        .pipe(plumber())
        .pipe(sass({
            outputStyle: "expanded"
        }))
        .pipe(gulp.dest('./css/'));
    done();
}

// スキン用 SCSS を各スキンフォルダごとにビルドするタスク
const scssBuildSkins = (done) => {
    gulp.src(['skins/**/*.scss', '!skins/skin-test/**'])
        .pipe(plumber())
        .pipe(sass({
            outputStyle: "expanded"
        }))
        .pipe(rename(function (path) {
            if ( path.dirname.indexOf('\\scss') !== -1 ) {
                return {
                    dirname: path.dirname+'/../css',
                    basename: path.basename,
                    extname: path.extname
                };
            }
            return {
                dirname: path.dirname,
                basename: path.basename,
                extname: path.extname
            };
        }))
        .pipe(gulp.dest('./skins'));
    done();
}

// SCSS ファイルの変更を監視して自動ビルドするタスク
const watchFiles = (done) => {
    gulp.watch('./scss/**/*.scss', gulp.series(scssBuildFront, scssBuildFontAwesome, scssBuildAdmin, scssBuildEditor, scssBuildEditorToCss, scssBuildBlock, scssBuildEditorPage, scssBuildEntryContent));
    gulp.watch(['skins/**/*.scss', '!skins/skin-test/**'], scssBuildSkins);
    done();
}

// デフォルトタスク（npx gulp で全 SCSS を一括ビルド）
exports.default = gulp.series(
    scssBuildFront, scssBuildFontAwesome, scssBuildAdmin, scssBuildEditor, scssBuildEditorToCss, scssBuildBlock, scssBuildEditorPage, scssBuildEntryContent, scssBuildSkins
);

// ファイル監視タスク（npx gulp watch で実行可能）
exports.watch = watchFiles;

// style.css のみをビルド（npx gulp style で実行可能）
exports.style = scssBuildStyleOnly;
