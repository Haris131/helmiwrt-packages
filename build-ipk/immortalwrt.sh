#!/bin/bash
#=================================================
# Description: DIY script
# Lisence: MIT
# Author: P3TERX
# Blog: https://p3terx.com
#=================================================

sed -i '$a src-git kenzo https://github.com/kenzok8/openwrt-packages' feeds.conf.default
sed -i '$a src-git small https://github.com/kenzok8/small' feeds.conf.default

# Clone community packages to package
[[ -d package ]] && mkdir package
pushd package

# HelmiWrt packages
git clone --depth=1 https://github.com/helmiau/helmiwrt-packages

# Out to openwrt dir
popd

rm package/helmiwrt-packages/luci-app-libernet-plus/Makefile
wget https://raw.githubusercontent.com/helmiau/helmiwrt-packages/main/luci-app-libernet-plus/Makefile -O package/helmiwrt-packages/luci-app-libernet-plus/Makefile

#-----------------------------------------------------------------------------
#   End of @helmiau terminal scripts additionals menu
#-----------------------------------------------------------------------------
