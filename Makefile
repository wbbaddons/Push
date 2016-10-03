WCF_FILES = $(shell find files_wcf -type f -not -name '*.babel')
BABEL = files_wcf/js/Bastelstu.be/_Push.babel

all: be.bastelstu.wcf.push.tar

be.bastelstu.wcf.push.tar: files_wcf.tar *.xml LICENSE
	tar cvf be.bastelstu.wcf.push.tar --numeric-owner --exclude-vcs -- files_wcf.tar *.xml LICENSE

files_wcf.tar: $(WCF_FILES) files_wcf/js/Bastelstu.be/_Push.babel
	# Note: This tar command line duplicates filenames inside the archive for the babeled files.
	#       GNU tar and WCF both extract the file added last, so we don't care.
	tar cvf files_wcf.tar --numeric-owner --exclude-vcs --exclude .babelrc --transform='s,^files_wcf/,,' --transform='s,.babel$$,.js,' -- $+

%.babel: %.js
	babel $< > $@

clean:
	-rm -f files_wcf.tar
	-rm -f $(BABEL)

distclean: clean
	-rm -f be.bastelstu.wcf.push.tar

.PHONY: distclean clean
