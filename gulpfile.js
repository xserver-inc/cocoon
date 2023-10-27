const gulp = require("gulp");
const sass = require("gulp-sass")(require("node-sass"));

const scssBuildFront = (done) => {
    gulp.src('./scss/style.scss')
        .pipe(sass({
            outputStyle: "expanded"
        }))
        .pipe(gulp.dest('./'));
    done();
}

const watchFiles = (done) => {
    gulp.watch('./scss/**/*.scss', scssBuildFront);
    done();
}

exports.default = gulp.series(
    scssBuildFront
);

exports.watch = watchFiles;
