WCF_FILES = $(shell find files_wcf -type f |sed 's/^files_wcf/files_wcf.out/')

all: be.bastelstu.wcf.push.tar

be.bastelstu.wcf.push.tar: files_wcf.tar *.xml LICENSE
	tar cvf be.bastelstu.wcf.push.tar --numeric-owner --exclude-vcs -- files_wcf.tar *.xml LICENSE

files_wcf.tar: $(WCF_FILES)
	tar cvf files_wcf.tar --numeric-owner --exclude-vcs --exclude .babelrc --transform='s,^files_wcf.out/,,' -- $+

files_wcf.out/%.js: files_wcf/%.js
	-@mkdir -p $$(dirname $@)
	yarn run babel $< --out-file $@

files_wcf.out/%: files_wcf/%
	-@mkdir -p $$(dirname $@)
	cp -a $< $@

clean:
	-rm -f files_wcf.tar
	-rm -rf files_wcf.out

distclean: clean
	-rm -f be.bastelstu.wcf.push.tar

.PHONY: distclean clean
