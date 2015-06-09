#!/bin/sh -x

# $2 gitDir
cd $2

# $1 action
# $2 gitDir
# $3 git commit message
# $4 git email
# $5 git username
if [ "$1" = "commit" ]; then
    git add .
    if [ "$4" != "" ]; then
        git config user.email $4;
        git config user.name $5;
    fi
    git commit -m"'commit $3'"
fi

# $1 action
if [ "$1" = "listconf" ]; then
    git config --list;
fi

# $1 action
# $2 git Dir
if [ "$1" = "init" ]; then
    git init $1;
fi

# $1 action
# $2 git Dir
if [ "$1" = "log" ]; then
    git log --all --pretty=format:'%h^%s^%cr' --abbrev-commit --date=relative
fi

# $1 action
# $2 git Dir
# $3 revision
if [ "$1" = "revert" ]; then
    git checkout $3
fi


# $1 action
# $2 git Dir
if [ "$1" = "pull" ]; then
    git pull $2
fi


# $1 action
# $2 git Dir
# $3 revision1
# $4 revision2
# $5 output
if [ "$1" = "diff" ]; then
    git diff --word-diff -U0 $3 $4
    #git diff --word-diff -U0  --numstat --word-diff-regex=. $3 $4
fi
