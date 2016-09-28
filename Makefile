WCF_FILES = $(shell find files_wcf -type f)

all: be.bastelstu.wcf.push.tar

be.bastelstu.wcf.push.tar: files_wcf.tar *.xml LICENSE
	tar cvf be.bastelstu.wcf.push.tar --numeric-owner --exclude-vcs -- files_wcf.tar *.xml LICENSE

files_wcf.tar: $(WCF_FILES)
	tar cvf files_wcf.tar --exclude-vcs --transform='s,files_wcf/,,' -- $(WCF_FILES)

clean:
	-rm -f files_wcf.tar

distclean: clean
	-rm -f be.bastelstu.wcf.push.tar

.PHONY: distclean clean
