#!/bin/bash
#=================================================
# Description: DIY script
# Lisence: MIT
# Author: P3TERX
# Blog: https://p3terx.com
#=================================================

# Clone community packages to package
mkdir -p package/community
pushd package/community

# HelmiWrt packages
git clone --depth=1 https://github.com/Haris131/helmiwrt-packages

git clone --depth=1 https://github.com/nosignals/openwrt-neko
sed -i "s|php7|php8|g" openwrt-neko/luci-app-neko/root/etc/neko/core/neko

# Out to openwrt dir
popd
