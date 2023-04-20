# Copyright (C) 2009 Mygod
#
# This is free software, licensed under the GNU General Public License v2.
# Updated & patched for OpenWrt by Chewy Mage <Haris131>
#

include $(TOPDIR)/rules.mk

PKG_NAME:=dnstt
PKG_VERSION:=0.20210424.0
PKG_RELEASE:=$(AUTORELEASE)

PKG_SOURCE:=$(PKG_NAME)-$(PKG_VERSION).tar.gz
PKG_SOURCE_URL:=https://codeload.github.com/Mygod/tar.gz/v$(PKG_VERSION)?
PKG_HASH:=75e7851d808fd1d3d2d80ff2d5dac7667d6a086e513c5243ca751f2e3d487a72

PKG_LICENSE:=GPL-3.0-only
PKG_LICENSE_FILES:=LICENSE
PKG_MAINTAINER:=Tianling Shen <cnsztl@immortalwrt.org>

PKG_BUILD_DEPENDS:=golang/host
PKG_BUILD_PARALLEL:=1
PKG_USE_MIPS16:=0

GO_PKG:=github.com/Mygod/dnstt/dnstt-client
GO_PKG_BUILD_PKG:=$(GO_PKG)
GO_PKG_LDFLAGS_X:= \
	$(GO_PKG)/constant.Version=$(PKG_VERSION) \
	$(GO_PKG)/constant.Commit=v$(PKG_VERSION)
GO_PKG_TAGS:=full

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

$(eval $(call GoBinPackage,$(PKG_NAME)))
$(eval $(call BuildPackage,$(PKG_NAME)))
