#!/usr/bin/env bash

RELEASE_BRANCH=$1

# fetch latest version
git checkout master
git checkout $RELEASE_BRANCH
git pull git@github.com:wutongwan/laravel-lego.git master

# run bower
echo "# clear bower components"
rm -rf public/components public/js public/.gitignore
echo "# update bower components"
bower update
echo "# remove useless assets"
find public/components \
	-path '*/.*' \
	-o -path '*/tests/*' \
	-o -path '*/test/*' \
	-o -path '*/src/*' \
	-o -path '*/docs/*' \
	-o -path '*/build/*' \
	-o -path '*/examples/*' \
	-o -path '*/benchmarks/*' \
	-o -path '*/flow/*' \
	-o -path '*/vendor/*' \
	-o -path '*/external/*' \
	-o -name '*.less' \
	-o -name '*.sass' \
	-o -name '*.scss' \
	-o -name '*.ts' \
	-o -name '*.md' \
	-o -name '*.yml' \
	-o -name '*.lock' \
	-o -name '*.txt' \
	-o -name '*.nuspec' \
	-o -name '*.ps1' \
	-o -name 'bower.json' \
	-o -name 'composer.json' \
	-o -name 'package.json' \
	-o -name 'component.json' \
	-o -name 'Gruntfile.js' \
	-o -name 'Gemfile' \
	-o -name 'package.js' \
	-o -name 'LICENCE*' \
	-o -name 'LICENSE*' \
	-o -name 'jquery.tinymce.*' \
	-o -name 'tinymce.jquery.*' \
	| xargs rm -rf

find public/components -type d -empty | xargs rm -rf

# generate ide helpers
php build/generate-ide-helper.php

# run mix
yarn prod

# create release commit
git add .
git commit -a -m "build $RELEASE_BRANCH" -q
git push git@github.com:wutongwan/laravel-lego.git $RELEASE_BRANCH

echo '-> Draft new release https://github.com/wutongwan/laravel-lego/releases/new'
