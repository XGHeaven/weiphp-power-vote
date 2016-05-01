from fabric.api import *

env.use_ssh_config = True

def check(path):
    with settings(warn_only=True):
        if run('test -d %s' % path).failed:
            clone(path)

def clone(path):
    run('git clone https://github.com/XGHeaven/weiphp-power-vote.git %s' % path);

def pull(path):
    with cd(path):
        run('git pull')

def chown(path):
    run('chown nginx:nginx -R %s' % path)

def deploy(path):
    check(path)
    pull(path)
    chown(path)
