const gulp = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const rename = require('gulp-rename');
const plumber = require('gulp-plumber');

const scssBuildFront = (done) => {
    gulp.src(['./scss/style.scss', './scss/animation/keyframes.scss', './scss/amp.scss'])
        .pipe(plumber())
        .pipe(sass({
            outputStyle: "expanded"
        }))
        .pipe(gulp.dest('./'));
    done();
}

const scssBuildFontAwesome = (done) => {
    gulp.src('./scss/fontawesome5.scss')
        .pipe(plumber())
        .pipe(sass({
            outputStyle: "expanded"
        }))
        .pipe(gulp.dest('./css/'));
    done();
}

const scssBuildAdmin = (done) => {
    gulp.src('./scss/admin.scss')
        .pipe(plumber())
        .pipe(sass({
            outputStyle: "expanded"
        }))
        .pipe(gulp.dest('./css/'));
    done();
}

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

const scssBuildBlock = (done) => {
    gulp.src('./scss/gutenberg-editor.scss')
        .pipe(plumber())
        .pipe(sass({
            outputStyle: "expanded"
        }))
        .pipe(gulp.dest('./css/'));
    done();
}

const scssBuildEditorPage = (done) => {
    gulp.src('./scss/editor-page.scss')
        .pipe(plumber())
        .pipe(sass({
            outputStyle: "expanded"
        }))
        .pipe(gulp.dest('./css/'));
    done();
}

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

const watchFiles = (done) => {
    gulp.watch('./scss/**/*.scss', gulp.series(scssBuildFront, scssBuildFontAwesome, scssBuildAdmin, scssBuildEditor, scssBuildBlock, scssBuildEditorPage));
    gulp.watch(['skins/**/*.scss', '!skins/skin-test/**'], scssBuildSkins);
    done();
}

exports.default = gulp.series(
    scssBuildFront, scssBuildFontAwesome, scssBuildAdmin, scssBuildEditor, scssBuildBlock, scssBuildEditorPage, scssBuildSkins
);

exports.watch = watchFiles;
