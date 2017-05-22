#!/usr/bin/env bash

echo "# clear bower components"
git branch -D release
git checkout -b release
git pull git@github.com:wutongwan/laravel-lego.git master

echo "# clear bower components"
rm -rf public/components
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
	| xargs rm -rf

git add .
git commit -a -m 'build release'
git push git@github.com:wutongwan/laravel-lego.git release --force

echo '-> Draft new release https://github.com/wutongwan/laravel-lego/releases/new'
