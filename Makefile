WCF_FILES = $(shell find files_wcf -type f)
TS_FILES = $(shell find ts/ -type f |sed 's/ts$$/js/g;s!^ts/!files_wcf/js/!')

all: be.bastelstu.wcf.push.tar

be.bastelstu.wcf.push.tar: files_wcf.tar files_update_212.tar *.xml LICENSE
	tar cvf $@ --numeric-owner --exclude-vcs -- $^

files_wcf.tar: $(WCF_FILES) $(TS_FILES)
files_update_212.tar: files_update_212/acp/be.bastelstu.wcf.push_2.1.2.php

%.tar:
	tar cvf $@ --numeric-owner --exclude-vcs -C $* -- $(^:$*/%=%)

files_wcf/js/%.js: ts/%.ts
	yarn run tsc

clean:
	-find files_wcf/js/ -iname '*.js' -delete
	-rm -f files_wcf.tar
	-rm -rf files_wcf.out

distclean: clean
	-rm -f be.bastelstu.wcf.push.tar

.PHONY: distclean clean
