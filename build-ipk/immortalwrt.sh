#!/bin/bash
#=================================================
# Description: DIY script
# Lisence: MIT
# Author: P3TERX
# Blog: https://p3terx.com
#=================================================

sed -i '$a src-git small https://github.com/kenzok8/small' feeds.conf.default

# Clone community packages to package
mkdir -p package/community
mkdir -p ipkg
pushd package/community

# HelmiWrt packages
git clone --depth=1 https://github.com/Haris131/helmiwrt-packages

# Out to openwrt dir
popd
