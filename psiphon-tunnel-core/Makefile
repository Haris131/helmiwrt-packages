# Copyright (C) 2020 Psiphon-Labs
#
# This is free software, licensed under the GNU General Public License v2.
# Updated & patched for OpenWrt by Chewy Mage <Haris131>
#

include $(TOPDIR)/rules.mk

PKG_NAME:=psiphon-tunnel-core
PKG_VERSION:=2.0.31
PKG_RELEASE:=1

PKG_SOURCE:=$(PKG_NAME)-$(PKG_VERSION).tar.gz
PKG_SOURCE_URL:=https://codeload.github.com/Psiphon-Labs/psiphon-tunnel-core/tar.gz/v$(PKG_VERSION)?
PKG_HASH:=75e1680d486d3e797796b0a00ef48abda6b82f70dd14584f8b69452aed7fa64d

PKG_BUILD_DEPENDS:=golang/host
PKG_BUILD_PARALLEL:=1
PKG_USE_MIPS16:=0

GO_PKG:=github.com/Psiphon-Labs/psiphon-tunnel-core
GO_PKG_BUILD_PKG:=$(GO_PKG)/ConsoleClient

include $(INCLUDE_DIR)/package.mk
include $(TOPDIR)/feeds/packages/lang/golang/golang-package.mk

define Package/$(PKG_NAME)
	TITLE:=Psiphon is an Internet censorship circumvention system.
	DEPENDS:=$(GO_ARCH_DEPENDS)
	URL:=https://github.com/Psiphon-Labs/psiphon-tunnel-core
endef

define Package/$(PKG_NAME)/description
  Psiphon is an Internet censorship circumvention system.

  The tunnel core project includes a tunneling client and server, which together implement key aspects of evading blocking and relaying client traffic through Psiphon and beyond censorship.

  All Psiphon open source projects, including the complete open source code for Android, iOS, and Windows clients may be found at www.github.com/Psiphon-Inc/psiphon.

  For more information about Psiphon Inc., please visit our web site at www.psiphon.ca.
endef

define Package/$(PKG_NAME)/install
	$(call GoPackage/Package/Install/Bin,$(PKG_INSTALL_DIR))

	$(INSTALL_DIR) $(1)/usr/bin/
	$(INSTALL_BIN) $(PKG_INSTALL_DIR)/usr/bin/ConsoleClient $(1)/usr/bin/psiphon-tunnel-core
endef

$(eval $(call GoBinPackage,$(PKG_NAME)))
$(eval $(call BuildPackage,$(PKG_NAME)))
