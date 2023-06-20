<!doctype html>
<html lang="en">
<head>
    <?php
        $title = "Configuration";
        include("head.php");
    ?>
</head>
<body>
<div id="app">
    <?php include('navbar.php'); ?>
    <div class="container-fluid">
        <div class="row py-2">
            <div class="col-lg-8 col-md-12 mx-auto mt-3">
                <div class="card">
                    <div class="card-header">
                        <div class="text-center">
                            <h3><i class="fa fa-gears"></i> Configuration</h3>
                        </div>
                        <hr>
                        <form @submit.prevent="getConfig">
                            <div class="form-group form-row my-auto">
                                <div class="col-lg-4 col-md-4 form-row py-1">
                                    <div class="col-lg-4 col-md-3 my-auto">
                                        <label class="my-auto">Mode</label>
                                    </div>
                                    <div class="col">
                                        <select class="form-control" v-model.number="config.mode" required>
                                            <option v-for="mode in sortedModes" :value="mode.value">{{ mode.name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 form-row py-1">
                                    <div class="col-lg-4 col-md-3 my-auto">
                                        <label class="my-auto">Config</label>
                                    </div>
                                    <div class="col">
                                        <select class="form-control" v-model="config.profile" required>
                                            <option v-for="profile in config.profiles" :value="profile">{{ profile }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-3 form-row py-1">
                                    <div class="col d-flex">
                                        <button type="submit" class="btn btn-secondary mr-1">Load</button>
                                        <button type="button" class="btn btn-danger ml-1" @click="deleteConfig">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="saveConfig">
                            <div class="form-row pb-lg-2">
                                <div class="col-md-6">
                                    <label>Mode</label>
                                    <select v-model.number="config.temp.mode" class="form-control" required>
                                        <option v-for="mode in sortedModes" :value="mode.value">{{ mode.name }}</option>
                                    </select>
                                </div>
                                <div v-if="config.temp.mode === 0" class="col-md-6 pt-md-4 pl-lg-3 my-auto">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" v-model="config.temp.modes[0].profile.enable_http" checked id="enable-http">
                                        <label class="form-check-label" for="enable-http">
                                            Enable HTTP Proxy
                                        </label>
                                    </div>
                                </div>
                                <div v-if="config.temp.mode === 1" class="col-md-6">
                                    <label>Protocol</label>
                                    <select class="form-control" v-model="config.temp.modes[1].profile.protocol" required>
                                        <option v-for="protocol in config.temp.modes[1].protocols" :value="protocol.value">{{ protocol.name }}</option>
                                    </select>
                                </div>
                            </div>

                            <div v-if="config.temp.mode === 0" class="ssh pb-lg-2">
                                <div v-if="config.temp.modes[0].profile.enable_http" class="proxy">
                                    <div class="form-row pb-lg-2">
                                        <div class="col-md-6">
                                            <label>Proxy IP</label>
                                            <input type="text" class="form-control" placeholder="192.168.1.1" v-model="config.temp.modes[0].profile.http.proxy.ip" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Proxy Port</label>
                                            <input type="number" class="form-control" placeholder="8080" v-model.number="config.temp.modes[0].profile.http.proxy.port" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Payload</label>
                                        <textarea class="form-control" v-model="config.temp.modes[0].profile.http.payload" rows="5" placeholder="GET http://libernet.tld/ HTTP/1.1[crlf][crlf]CONNECT [host_port] HTTP/1.1[crlf]Connection: keep-allive[crlf][crlf]" required></textarea>
                                    </div>
                                </div>
                                <div class="form-row pb-lg-2">
                                    <div class="col-md-6">
                                        <label>Server Host</label>
                                        <input type="text" class="form-control" placeholder="Host/IP" v-model="config.temp.modes[0].profile.host" @input="resolveServerHost" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Server IP</label>
                                        <input type="text" class="form-control" placeholder="192.168.1.1" v-model="config.temp.modes[0].profile.ip" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Server Port</label>
                                        <input type="number" class="form-control" placeholder="443" v-model.number="config.temp.modes[0].profile.port" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <label>Username</label>
                                        <input type="text" class="form-control" placeholder="Username" v-model="config.temp.modes[0].profile.username" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Password</label>
                                        <input type="text" class="form-control" placeholder="Password" v-model="config.temp.modes[0].profile.password" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>UDPGW Port</label>
                                        <input type="number" class="form-control" placeholder="7300" v-model.number="config.temp.modes[0].profile.udpgw.port" required>
                                    </div>
                                </div>
                            </div>

                            <div v-if="config.temp.mode === 1" class="ssh-ssl pb-lg-2">
                                <div class="form-row pb-lg-2">
                                    <div class="col-md-6">
                                        <label>Server Host</label>
                                        <input type="text" class="form-control" placeholder="Host/IP" v-model="config.temp.modes[1].profile.host" @input="resolveServerHost" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Server IP</label>
                                        <input type="text" class="form-control" placeholder="192.168.1.1" v-model="config.temp.modes[1].profile.ip" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Server Port</label>
                                        <input type="number" class="form-control" placeholder="443" v-model.number="config.temp.modes[1].profile.port" required>
                                    </div>
                                </div>
                                <div class="form-row pb-lg-2">
                                    <div class="col-md-6">
                                        <label>Username</label>
                                        <input type="text" class="form-control" placeholder="Username" v-model="config.temp.modes[1].profile.username" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Password</label>
                                        <input type="text" class="form-control" placeholder="Password" v-model="config.temp.modes[1].profile.password" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>SNI</label>
                                        <input type="text" class="form-control" placeholder="www.bug.com" v-model="config.temp.modes[1].profile.sni" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>UDPGW Port</label>
                                        <input type="number" class="form-control" placeholder="7300" v-model.number="config.temp.modes[1].profile.udpgw.port" required>
                                    </div>
                                </div>
                            </div>

                            <div v-if="config.temp.mode === 2" class="openvpn pb-lg-2">
                                <div class="form-row pb-lg-2">
                                    <label>Import OVPN from file</label>
                                    <div class="col-md-12 custom-file">
                                        <input type="file" class="custom-file-input" accept=".ovpn, .conf" id="ovpn-file" @change="importOvpnConfig">
                                        <label class="custom-file-label" for="ovpn-file">Choose file</label>
                                    </div>
                                </div>
                                <div class="form-row pb-lg-2">
                                    <div class="col-md-12">
                                        <label>OVPN</label>
                                        <textarea class="form-control" rows="10" v-model="config.temp.modes[2].profile.ovpn" required></textarea>
                                    </div>
                                </div>
                                <div v-if="openvpn_auth_user_pass" class="form-row pb-lg-2">
                                    <div class="col-md-6">
                                        <label>Username</label>
                                        <input type="text" class="form-control" placeholder="Username" v-model="config.temp.modes[2].profile.username" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Password</label>
                                        <input type="text" class="form-control" placeholder="Password" v-model="config.temp.modes[2].profile.password" required>
                                    </div>
                                </div>
                                <div class="form-row pb-lg-2">
                                    <div class="col-md-12 pl-4">
                                        <input class="form-check-input" type="checkbox" v-model="config.temp.modes[2].profile.ssl" id="enable-ssl">
                                        <label class="form-check-label" for="enable-ssl">
                                            Enable SSL
                                        </label>
                                    </div>
                                </div>
                                <div v-if="config.temp.modes[5].profile.ssl" class="form-row pb-lg-2">
                                    <div class="col-md-4">
                                        <label>SNI</label>
                                        <input type="text" class="form-control" placeholder="www.bug.com" v-model="config.temp.modes[2].profile.sni" required>
                                    </div>
                                </div>
                            </div>

                            <div v-if="config.temp.mode === 3" class="ssh-ws-cdn pb-lg-2">
                                <div class="form-row proxy">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Payload</label>
                                            <textarea class="form-control" v-model="config.temp.modes[3].profile.http.payload" rows="5" placeholder="CONNECT wss://www.bugcdn.com/ HTTP/1.1[crlf]Host: [host_port] HTTP/1.1[crlf]Upgrade: websocket[crlf]Connection: keep-alive[crlf][crlf]" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row pb-lg-2">
                                    <div class="col-md-6">
                                        <label>Server Host</label>
                                        <input type="text" class="form-control" placeholder="Host/IP" v-model="config.temp.modes[3].profile.host" @input="resolveServerHost" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Server IP</label>
                                        <input type="text" class="form-control" placeholder="192.168.1.1" v-model="config.temp.modes[3].profile.ip" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Server Port</label>
                                        <input type="number" class="form-control" placeholder="443" v-model.number="config.temp.modes[3].profile.port" required>
                                    </div>
                                </div>
                                <div class="form-row pb-lg-2">
                                    <div class="col-md-4">
                                        <label>Username</label>
                                        <input type="text" class="form-control" placeholder="Username" v-model="config.temp.modes[3].profile.username" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Password</label>
                                        <input type="text" class="form-control" placeholder="Password" v-model="config.temp.modes[3].profile.password" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>UDPGW Port</label>
                                        <input type="number" class="form-control" placeholder="7300" v-model.number="config.temp.modes[3].profile.udpgw.port" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <label>CDN SNI</label>
                                        <input type="text" class="form-control" placeholder="www.bugcdn.com" v-model="config.temp.modes[3].profile.http.cdn.sni" @input="resolveServerHost" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>CDN IP</label>
                                        <input type="text" class="form-control" placeholder="192.168.553.123" v-model="config.temp.modes[3].profile.http.cdn.ip" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>CDN Port</label>
                                        <input type="number" class="form-control" placeholder="443" v-model.number="config.temp.modes[3].profile.http.cdn.port" required>
                                    </div>
                                </div>
                            </div>

                            <div v-if="config.temp.mode === 4" class="ssh-slowdns pb-lg-2">
                                <div class="form-row pb-lg-2">
                                    <div class="col-md-6">
                                        <label>Server Host</label>
                                        <input type="text" class="form-control" placeholder="Host/IP" v-model="config.temp.modes[4].profile.host" @input="resolveServerHost" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Server IP</label>
                                        <input type="text" class="form-control" placeholder="192.168.1.1" v-model="config.temp.modes[4].profile.ip" required>
                                    </div>
                                </div>
                                <div class="form-row pb-lg-2">
                                    <div class="col-md-4">
                                        <label>Username</label>
                                        <input type="text" class="form-control" placeholder="Username" v-model="config.temp.modes[4].profile.username" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Password</label>
                                        <input type="text" class="form-control" placeholder="Password" v-model="config.temp.modes[4].profile.password" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>UDPGW Port</label>
                                        <input type="number" class="form-control" placeholder="7300" v-model.number="config.temp.modes[4].profile.udpgw.port" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <label>DNS</label>
                                        <input type="text" class="form-control" placeholder="1.1.1.1" v-model="config.temp.modes[4].profile.dns" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>NS</label>
                                        <input type="text" class="form-control" placeholder="ns.libernet.tld" v-model="config.temp.modes[4].profile.ns" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Pubkey</label>
                                        <input type="text" class="form-control" placeholder="pubkey" v-model="config.temp.modes[4].profile.pubkey" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group pb-lg-2 text-center">
                                <label>Config Name</label>
                                <input type="text" class="form-control text-center" placeholder="Profil-Name" v-model="config.temp.profile" required>
                            </div>
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary form-control">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include('footer.php'); ?>
    </div>
</div>
<?php include("javascript.php"); ?>
<script src="js/config.js"></script>
</body>
</html>
