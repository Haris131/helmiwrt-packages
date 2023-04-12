#
# Libernet Software made by Lutfa Ibtihaji Ilham <https://github.com/lutfailham96/libernet>
# LuCI Libernet App by Helmi Amirudin <https://www.helmiau.com>
#
# Copyright (C) 2021 Helmi Amirudin <https://www.helmiau.com>
# This is free software, licensed under the Apache License, Version 2.0
#

include $(TOPDIR)/rules.mk

LUCI_TITLE:=LuCI Libernet Plus SSH App
LUCI_PKGARCH:=all
LUCI_DEPENDS:=+coreutils +coreutils-stdbuf +jq +ip-full +kmod-tun +openssh-client \
	+php7 +php7-cgi +php7-mod-json +php7-mod-session +python3 +stunnel +stubby \
	+badvpn +corkscrew +sshpass +bash +curl +librt +libpthread +screen

PKG_NAME:=luci-app-libernet-plus-ssh
PKG_VERSION:=2.1
PKG_RELEASE:=2

define Package/$(PKG_NAME)
	$(call Package/luci/webtemplate)
	TITLE:=$(LUCI_TITLE)
	DEPENDS:=$(LUCI_DEPENDS)
endef

define Package/$(PKG_NAME)/description
	LuCI version of Libernet, but with some mods and additions.
endef

define Package/$(PKG_NAME)/install
	$(INSTALL_DIR) $(1)/usr/lib/lua/luci
	cp -pR ./luasrc/* $(1)/usr/lib/lua/luci
	$(INSTALL_DIR) $(1)/
	cp -pR ./root/* $(1)/
	chmod -R 755 /root/www/*
	chmod -R 755 /root/root/*
	chmod -R 755 /root/root/libernet/*
	chmod -R 755 /root/root/libernet/bin/*
	chmod -R 755 /root/root/usr/bin/*
endef

define Package/$(PKG_NAME)/postinst
#!/bin/sh
	[ -d /tmp/luci-modulecache ] rm -rf /tmp/luci-modulecache
	find /tmp -type f -name 'luci-indexcache.*' -exec rm -f {};
	chmod -R 755 /usr/lib/lua/luci/controller/*
	chmod -R 755 /usr/lib/lua/luci/view/*
	chmod -R 755 /www/*
	chmod -R 755 /www/libernet/*
	chmod -R 755 /root/libernet/*
	chmod -R 755 /root/libernet/bin/*
	chmod -R 755 /usr/bin/hsize
	chmod -R 755 /usr/bin/dnstt-client
	# Autofix download index.php, index.html
	if ! grep -q ".php=/usr/bin/php-cgi" /etc/config/uhttpd; then
		echo -e "Enabling uhttp php execution"
		uci set uhttpd.main.interpreter='.php=/usr/bin/php-cgi'
		uci add_list uhttpd.main.index_page='index.php'
		uci commit uhttpd
		echo -e "Restarting uhttp service"
		/etc/init.d/uhttpd restart
	fi
	if ! uci get network.libernet > /dev/null 2>&1; then
		echo "Configuring Libernet firewall"
		uci set network.libernet=interface
		uci set network.libernet.proto='none'
		uci set network.libernet.ifname='tun1'
		uci commit
		uci add firewall zone
		uci set firewall.@zone[-1].network='libernet'
		uci set firewall.@zone[-1].name='libernet'
		uci set firewall.@zone[-1].masq='1'
		uci set firewall.@zone[-1].mtu_fix='1'
		uci set firewall.@zone[-1].input='REJECT'
		uci set firewall.@zone[-1].forward='REJECT'
		uci set firewall.@zone[-1].output='ACCEPT'
		uci commit
		uci add firewall forwarding
		uci set firewall.@forwarding[-1].src='lan'
		uci set firewall.@forwarding[-1].dest='libernet'
		uci commit
		echo -e "Restarting network service"
		/etc/init.d/network restart
    fi
	[ -d /usr/lib/php7 ] [ ! -d /usr/lib/php ] ln -sf /usr/lib/php7 /usr/lib/php
exit 0
endef

define Package/$(PKG_NAME)/postrm
#!/bin/sh
	if [ -d /www/libernet ] ; then
		rm -rf /www/libernet
	fi
exit 0
endef

include $(TOPDIR)/feeds/luci/luci.mk

$(eval $(call BuildPackage,$(PKG_NAME)))
