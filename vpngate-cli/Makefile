# Copyright (C) 2019 saihon
#
# This is free software, licensed under the GNU General Public License v2.
# Updated & patched for OpenWrt by Chewy Mage <Haris131>
#

include $(TOPDIR)/rules.mk

PKG_NAME:=vpngate-cli
PKG_VERSION:=0.0.7
PKG_RELEASE:=2

PKG_SOURCE:=$(PKG_NAME)-$(PKG_VERSION).tar.gz
PKG_SOURCE_URL:=https://codeload.github.com/saihon/vpngate-cli/tar.gz/v$(PKG_VERSION)?
PKG_HASH:=1a208f09684c7321cd45f6eb4e140185a5e07e528bbaa7c8c0631cafe566bc80

PKG_BUILD_DEPENDS:=golang/host
PKG_BUILD_PARALLEL:=1
PKG_USE_MIPS16:=0

GO_PKG:=github.com/saihon/vpngate-cli
GO_PKG_BUILD_PKG:=$(GO_PKG)/cmd/vpngate-cli

include $(INCLUDE_DIR)/package.mk
include $(TOPDIR)/feeds/packages/lang/golang/golang-package.mk

define Package/$(PKG_NAME)
	TITLE:=vpngate
	DEPENDS:=$(GO_ARCH_DEPENDS)
	URL:=https://www.vpngate.net
endef

define Package/$(PKG_NAME)/install
	$(call GoPackage/Package/Install/Bin,$(PKG_INSTALL_DIR))

	$(INSTALL_DIR) $(1)/usr/bin/
	$(INSTALL_BIN) $(PKG_INSTALL_DIR)/usr/bin/$(PKG_NAME) $(1)/usr/bin/$(PKG_NAME)
endef

$(eval $(call GoBinPackage,$(PKG_NAME)))
$(eval $(call BuildPackage,$(PKG_NAME)))
