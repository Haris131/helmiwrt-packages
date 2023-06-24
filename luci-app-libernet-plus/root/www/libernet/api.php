<?php
    include('config.inc.php');

    function json_response($data) {
        $resp = array(
            'status' => 'OK',
            'data' => $data
        );
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    function get_profiles($mode) {
        global $libernet_dir;
        $profiles = array();
        if ($handle = opendir($libernet_dir.'/bin/config/'.$mode.'/')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) == 'json') {
                    array_push($profiles, preg_replace('/\\.[^.\\s]{3,4}$/', '', $file));
                }
            }
            closedir($handle);
        }
        sort($profiles);
        json_response($profiles);
    }

    function get_config($mode, $profile) {
        global $libernet_dir;
        $data = null;
        $config = null;
        if ($profile) {
            $config = file_get_contents($libernet_dir.'/bin/config/'.$mode.'/'.$profile.'.json');
        } else {
            $system_config = file_get_contents($libernet_dir.'/system/config.json');
            $system_config = json_decode($system_config);
            $config = file_get_contents($libernet_dir.'/bin/config/'.$mode.'/'.$system_config->tunnel->profile->$mode.'.json');
        }
        $data = json_decode($config);
        json_response($data);
    }

    function set_auto_start($status) {
        global $libernet_dir;
        $system_config = file_get_contents($libernet_dir.'/system/config.json');
        $system_config = json_decode($system_config);
        if ($status) {
            // enable auto start
            exec('export LIBERNET_DIR="'.$libernet_dir.'" && '.$libernet_dir.'/bin/service.sh -ea');
            $system_config->tunnel->autostart = true;
        } else {
            // disable auto start
            exec('export LIBERNET_DIR="'.$libernet_dir.'" && '.$libernet_dir.'/bin/service.sh -da');
            $system_config->tunnel->autostart = false;
        }
        $system_config = json_encode($system_config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($libernet_dir.'/system/config.json', $system_config);
    }

    if (isset($_POST)) {
        $json = json_decode(file_get_contents('php://input'), true);
        switch ($json['action']) {
            case 'get_system_config':
                $system_config = file_get_contents($libernet_dir.'/system/config.json');
                $data = json_decode($system_config);
                json_response($data);
                break;
            case 'get_ssh_config':
                $profile = $json['profile'];
                get_config('ssh', $profile);
                break;
            case 'get_sshl_config':
                $profile = $json['profile'];
                get_config('ssh_ssl', $profile);
                break;
            case 'get_openvpn_config':
                $profile = $json['profile'];
                get_config('openvpn', $profile);
                break;
            case 'get_sshwscdn_config':
                $profile = $json['profile'];
                get_config('ssh_ws_cdn', $profile);
                break;
            case 'get_sshslowdns_config':
                $profile = $json['profile'];
                get_config('ssh_slowdns', $profile);
                break;
            case 'get_ssh_configs':
                get_profiles('ssh');
                break;
            case 'get_sshl_configs':
                get_profiles('ssh_ssl');
                break;
            case 'get_openvpn_configs':
                get_profiles('openvpn');
                break;
            case 'get_sshwscdn_configs':
                get_profiles('ssh_ws_cdn');
				break;
			case 'get_sshslowdns_configs':
                get_profiles('ssh_slowdns');
				break;
			case 'restart_libernet':
                $system_config = file_get_contents($libernet_dir.'/system/config.json');
                $system_config = json_decode($system_config);
                exec('export LIBERNET_DIR='.$libernet_dir.' && '.$libernet_dir.'/bin/service.sh -rl');
                json_response('Libernet service started');
                break;
            case 'start_libernet':
                $system_config = file_get_contents($libernet_dir.'/system/config.json');
                $system_config = json_decode($system_config);
                exec('export LIBERNET_DIR='.$libernet_dir.' && '.$libernet_dir.'/bin/service.sh -sl');
                json_response('Libernet service started');
                break;
            case 'cancel_libernet':
                exec('export LIBERNET_DIR="'.$libernet_dir.'" && '.$libernet_dir.'/bin/service.sh -cl');
                json_response('Libernet service canceled');
                break;
            case 'stop_libernet':
                exec('export LIBERNET_DIR="'.$libernet_dir.'" && '.$libernet_dir.'/bin/service.sh -ds');
                json_response('Libernet service stopped');
                break;
            case 'get_dashboard_info':
                $status = file_get_contents($libernet_dir.'/log/status.log');
                $log = file_get_contents($libernet_dir.'/log/service.log');
                $connected = file_get_contents($libernet_dir.'/log/connected.log');
                $system_config = file_get_contents($libernet_dir.'/system/config.json');
                $tundev = json_decode($system_config)->tun2socks->dev;
                // use hard coded tun device
                if (file_exists("/usr/bin/hsize")) {
                	exec("ifconfig $tundev | grep 'bytes:' | awk -F ':' '{print $2}' | awk -F ' ' '{print $1}' | hsize", $rx);
                    exec("ifconfig $tundev | grep 'bytes:' | awk -F ':' '{print $3}' | awk -F ' ' '{print $1}' | hsize", $tx);
                } else {
                	exec("ifconfig $tundev | grep 'bytes:' | awk '{print $3, $4}' | sed 's/(//g; s/)//g'", $rx);
                    exec("ifconfig $tundev | grep 'bytes:' | awk '{print $7, $8}' | sed 's/(//g; s/)//g'", $tx);
                    }
                json_response(array(
                    'status' => intval($status),
                    'log' => $log,
                    'connected' => $connected,
                    'total_data' => [
                        'tx' => implode($tx),
                        'rx' => implode($rx),
                    ]
                ));
                break;
            case 'save_config':
                if (isset($json['data'])) {
                    $system_config = file_get_contents($libernet_dir.'/system/config.json');
                    $system_config = json_decode($system_config);
                    $data = $json['data'];
                    $mode = $data['mode'];
                    $profile = $data['profile'];
                    $config = $data['config'];
                    switch ($mode) {
                        // ssh
                        case 0:
                            file_put_contents($libernet_dir.'/bin/config/ssh/'.$profile.'.json', json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                            json_response('SSH config saved');
                            break;
                        // ssh-ssl
                        case 1:
                            file_put_contents($libernet_dir.'/bin/config/ssh_ssl/'.$profile.'.json', json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                            json_response('SSH-SSL config saved');
                            break;
                        // openvpn
                        case 2:
                            file_put_contents($libernet_dir.'/bin/config/openvpn/'.$profile.'.json', json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                            json_response('OpenVPN config saved');
                            break;
                        // ssh-ws-cdn
                        case 3:
                            file_put_contents($libernet_dir.'/bin/config/ssh_ws_cdn/'.$profile.'.json', json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                            json_response('SSH-WS-CDN config saved');
                            break;
                        // ssh
                        case 4:
                            file_put_contents($libernet_dir.'/bin/config/ssh_slowdns/'.$profile.'.json', json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                            json_response('SSH-SlowDNS config saved');
                            break;
                    }
                }
                break;
            case 'apply_config':
                if (isset($json['data'])) {
                    $system_config = file_get_contents($libernet_dir.'/system/config.json');
                    $system_config = json_decode($system_config);
                    $data = $json['data'];
                    $profile = $data['profile'];
                    $mode = $data['mode'];
                    $tun2socks_legacy = $data['tun2socks_legacy'];
                    $dns_resolver = $data['dns_resolver'];
                    $memory_cleaner = $data['memory_cleaner'];
                    $ping_loop = $data['ping_loop'];
					$auto_recon = $data['auto_recon'];
                    switch ($mode) {
                        // ssh
                        case 0:
                            $ssh_config = file_get_contents($libernet_dir.'/bin/config/ssh/'.$profile.'.json');
                            $ssh_config = json_decode($ssh_config);
                            $system_config->tunnel->profile->ssh = $profile;
                            $system_config->server = $ssh_config->ip;
                            $system_config->cdn_server =  $ssh_config->http->proxy->ip;
                            $system_config->tun2socks->udpgw->ip = $ssh_config->udpgw->ip;
                            $system_config->tun2socks->udpgw->port = $ssh_config->udpgw->port;
                            break;
                        // ssh-ssl
                        case 1:
                            $sshl_config = file_get_contents($libernet_dir.'/bin/config/ssh_ssl/'.$profile.'.json');
                            $sshl_config = json_decode($sshl_config);
                            $system_config->tunnel->profile->ssh_ssl = $profile;
                            $system_config->server = $sshl_config->ip;
                            $system_config->cdn_server =  "";
                            $system_config->tun2socks->udpgw->ip = $sshl_config->udpgw->ip;
                            $system_config->tun2socks->udpgw->port = $sshl_config->udpgw->port;
                            break;
                        // openvpn
                        case 2:
                            $openvpn_config = file_get_contents($libernet_dir.'/bin/config/openvpn/'.$profile.'.json');
                            $openvpn_config = json_decode($openvpn_config);
                            $system_config->tunnel->profile->openvpn = $profile;
                            $system_config->cdn_server =  "";
                            $system_config->tun2socks->udpgw->ip = $openvpn_config->udpgw->ip;
                            $system_config->tun2socks->udpgw->port = $openvpn_config->udpgw->port;
                            break;
                        // ssh-ws-cdn
                        case 3:
                            $ssh_ws_cdn_config = file_get_contents($libernet_dir.'/bin/config/ssh_ws_cdn/'.$profile.'.json');
                            $ssh_ws_cdn_config = json_decode($ssh_ws_cdn_config);
                            $system_config->tunnel->profile->ssh_ws_cdn = $profile;
                            $system_config->server = $ssh_ws_cdn_config->ip;
                            $system_config->cdn_server = $ssh_ws_cdn_config->http->cdn->ip;
                            $system_config->tun2socks->udpgw->ip = $ssh_ws_cdn_config->udpgw->ip;
                            $system_config->tun2socks->udpgw->port = $ssh_ws_cdn_config->udpgw->port;
                            break;
                        // ssh slowdns
                        case 4:
                            $ssh_slowdns_config = file_get_contents($libernet_dir.'/bin/config/ssh_slowdns/'.$profile.'.json');
                            $ssh_slowdns_config = json_decode($ssh_slowdns_config);
                            $system_config->tunnel->profile->ssh_slowdns = $profile;
                            $system_config->server = $ssh_slowdns_config->ip;
                            $system_config->cdn_server = $ssh_slowdns_config->dns;
                            $system_config->tun2socks->udpgw->ip = $ssh_slowdns_config->udpgw->ip;
                            $system_config->tun2socks->udpgw->port = $ssh_slowdns_config->udpgw->port;
                            break;
                    }
                    $system_config->tunnel->mode = $mode;
                    $system_config->tun2socks->legacy = $tun2socks_legacy;
                    $system_config->tunnel->dns_resolver = $dns_resolver;
                    $system_config->system->memory_cleaner = $memory_cleaner;
                    $system_config->tunnel->ping_loop = $ping_loop;
					$system_config->tunnel->auto_recon = $auto_recon;
                    $system_config = json_encode($system_config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    file_put_contents($libernet_dir.'/system/config.json', $system_config);
                    json_response('Configuration applied');
                }
                break;
            case 'delete_config':
                if (isset($json['data'])) {
                    $data = $json['data'];
                    $mode = $data['mode'];
                    $profile = $data['profile'];
                    switch ($mode) {
                        case 0:
                            unlink($libernet_dir.'/bin/config/ssh/'.$profile.'.json');
                            json_response('SSH config removed');
                            break;
                        case 1:
                            unlink($libernet_dir.'/bin/config/ssh_ssl/'.$profile.'.json');
                            unlink($libernet_dir.'/bin/config/stunnel/ssh/'.$profile.'.conf');
                            json_response('SSH-SSL config removed');
                            break;
                        case 2:
                            unlink($libernet_dir.'/bin/config/openvpn/'.$profile.'.json');
                            unlink($libernet_dir.'/bin/config/openvpn/'.$profile.'.ovpn');
                            unlink($libernet_dir.'/bin/config/openvpn/'.$profile.'.txt');
                            unlink($libernet_dir.'/bin/config/stunnel/openvpn/'.$profile.'.conf');
                            json_response('OpenVPN config removed');
                            break;
                        case 3:
                            unlink($libernet_dir.'/bin/config/ssh_ws_cdn/'.$profile.'.json');
                            unlink($libernet_dir.'/bin/config/stunnel/ssh_ws_cdn/'.$profile.'.conf');
                            json_response('SSH-WS-CDN config removed');
                            break;
                        case 4:
                            unlink($libernet_dir.'/bin/config/ssh_slowdns/'.$profile.'.json');
                            json_response('SSH-SlowDNS config removed');
                            break;
                    }
                }
                break;
            case 'set_auto_start':
                $status = $json['status'];
                set_auto_start($status);
                if ($status) {
                    json_response("Libernet service auto start enabled");
                } else {
                    json_response("Libernet service auto start disabled");
                }
                break;
            case 'resolve_host':
                $output = null;
                $retval = null;
                $host = $json['host'];
                exec("ping -4 -c 1 -W 1 ".$host." | grep PING | awk '{print $3}' | sed 's/(//g; s/)//g; s/://g' | sed -n '1p'", $output, $retval);
                if (!$retval) {
                    json_response($output);
                }
                break;
        }
    }
?>
