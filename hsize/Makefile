# Copyright (C) 2020 gonejack
#
# This is free software, licensed under the GNU General Public License v2.
# Updated & patched for OpenWrt by Chewy Mage <Haris131>
#

include $(TOPDIR)/rules.mk

PKG_NAME:=hsize
PKG_VERSION:=1.3.2
PKG_RELEASE:=2

PKG_SOURCE:=$(PKG_NAME)-$(PKG_VERSION).tar.gz
PKG_SOURCE_URL:=https://codeload.github.com/gonejack/hsize/tar.gz/v$(PKG_VERSION)?
PKG_HASH:=a6022e456dd15baa41424ede471660aa3fc95cb25ad1df401fc20732d99a6c1a

PKG_BUILD_DEPENDS:=golang/host
PKG_BUILD_PARALLEL:=1
PKG_USE_MIPS16:=0

GO_PKG:=github.com/gonejack/hsize
GO_PKG_BUILD_PKG:=$(GO_PKG)

include $(INCLUDE_DIR)/package.mk
include $(TOPDIR)/feeds/packages/lang/golang/golang-package.mk

define Package/$(PKG_NAME)
	TITLE:=Command line tool for human readable size
	DEPENDS:=$(GO_ARCH_DEPENDS)
	URL:=https://github.com/gonejack/hsize
endef

define Package/$(PKG_NAME)/install
	$(call GoPackage/Package/Install/Bin,$(PKG_INSTALL_DIR))

	$(INSTALL_DIR) $(1)/usr/bin/
	$(INSTALL_BIN) $(PKG_INSTALL_DIR)/usr/bin/hsize $(1)/usr/bin/hsize
endef

$(eval $(call GoBinPackage,$(PKG_NAME)))
$(eval $(call BuildPackage,$(PKG_NAME)))
