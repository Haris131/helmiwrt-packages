# Copyright (C) 2020 Mygod
#
# This is free software, licensed under the GNU General Public License v2.
# Updated & patched for OpenWrt by Chewy Mage <Haris131>
#

include $(TOPDIR)/rules.mk

PKG_NAME:=dnstt
PKG_VERSION:=1.20220208.0
PKG_RELEASE:=2

PKG_SOURCE:=$(PKG_NAME)-$(PKG_VERSION).tar.gz
PKG_SOURCE_URL:=https://codeload.github.com/Haris131/dnstt/tar.gz/v$(PKG_VERSION)?
PKG_HASH:=0c0c46040039be90f5a7851e03ac7b9e0c7c66ea76875059f07bcdde52f482c3

PKG_BUILD_DEPENDS:=golang/host
PKG_BUILD_PARALLEL:=1
PKG_USE_MIPS16:=0

GO_PKG:=github.com/Haris131/dnstt
GO_PKG_BUILD_PKG:=$(GO_PKG)/dnstt-client

include $(INCLUDE_DIR)/package.mk
include $(TOPDIR)/feeds/packages/lang/golang/golang-package.mk

define Package/$(PKG_NAME)
	TITLE:=dnstt is an application-layer tunnel that runs in userspace.
	SECTION:=net
	CATEGORY:=Network
	SUBMENU:=Web Servers/Proxies
	DEPENDS:=$(GO_ARCH_DEPENDS) +ca-bundle
	URL:=https://www.bamsoftware.com/software/dnstt/
endef

define Package/$(PKG_NAME)/description
  dnstt is an application-layer tunnel that runs in userspace.
  It doesn't provide a TUN/TAP interface; it only hooks up a local TCP port with a remote TCP port (like netcat or `ssh -L`) by way of a DNS resolver.
  It does not itself provide a SOCKS or HTTP proxy interface, but you can get the same effect by running a proxy on the tunnel server and having the tunnel terminate at the proxy.
endef

define Package/$(PKG_NAME)/install
	$(call GoPackage/Package/Install/Bin,$(PKG_INSTALL_DIR))

	$(INSTALL_DIR) $(1)/usr/bin/
	$(INSTALL_BIN) $(PKG_INSTALL_DIR)/usr/bin/dnstt-client $(1)/usr/bin/dnstt-client
endef

$(eval $(call GoBinPackage,$(PKG_NAME)))
$(eval $(call BuildPackage,$(PKG_NAME)))
