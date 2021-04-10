#!/bin/sh

cd /usr/paysdufle.fr/src

hasChanged=0
git remote update && git status -uno | grep -q 'Your branch is behind' && hasChanged=1
if [ $hasChanged = 1 ]; then
    git pull
    echo "Successfully updated!";
else
    echo "Already up-to-date!"
fi

composer install && php ./build.php