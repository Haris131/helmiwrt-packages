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

# kenzo8 small packages
#svn co https://github.com/kenzok8/small/trunk/trojan-go
#svn co https://github.com/kenzok8/small/trunk/simple-obfs
#svn co https://github.com/kenzok8/small/trunk/v2ray-core

# Out to openwrt dir
popd
