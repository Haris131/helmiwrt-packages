const app = new Vue({
    el: '#app',
    data() {
        return {
            config: {
                mode: 0,
                profile: "",
                profiles: [],
                temp: {
                    mode: 0,
                    profile: "",
                    modes: [
                        {
                            value: 0,
                            name: "SSH",
                            profile: {
                                ip: "",
                                host: "",
                                port: null,
                                username: "",
                                password: "",
                                udpgw: {
                                    ip: "127.0.0.1",
                                    port: null
                                },
                                enable_http: true,
                                http: {
                                    buffer: 32767,
                                    ip: "127.0.0.1",
                                    port: 9876,
                                    info: "HTTP Proxy",
                                    payload: "",
                                    proxy: {
                                        ip: "",
                                        port: null
                                    }
                                }
                            }
                        },
                        {
                            value: 1,
                            name: "SSH-SSL",
                            profile: {
                                ip: "",
                                host: "",
                                port: null,
                                username: "",
                                password: "",
                                sni: "",
                                udpgw: {
                                    ip: "127.0.0.1",
                                    port: null
                                }
                            }
                        },
                        {
                            value: 2,
                            name: "SSH-WS-CDN",
                            profile: {
                                ip: "",
                                host: "",
                                port: null,
                                username: "",
                                password: "",
                                udpgw: {
                                    ip: "127.0.0.1",
                                    port: null
                                },
                                enable_http: true,
                                http: {
                                    buffer: 32767,
                                    ip: "127.0.0.1",
                                    port: 9876,
                                    info: "HTTP Proxy",
                                    payload: "",
                                    proxy: {
                                        ip: "127.0.0.1",
                                        port: 10443,
                                    },
                                    cdn: {
                                        sni: "",
                                        ip: "",
                                        port: null,
                                    },
                                }
                            }
                        },
                        {
                            value: 3,
                            name: "SSH-SlowDNS",
                            profile: {
                                ip: "",
                                host: "",
                                username: "",
                                password: "",
                                dns: "",
                                ns: "",
                                pubkey: "",
                                udpgw: {
                                    ip: "127.0.0.1",
                                    port: null
                                }
                            }
                        },
                    ]
                },
                system: {}
            }
        }
    },
    computed: {
        sortedModes() {
            const modes = [...this.config.temp.modes];
            return modes.sort((a, b) => (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0))
        }
    },
    watch: {
        'config.mode': function (mode) {
            this.getProfiles(mode)
            this.config.profile = ""
        },
        'config.temp.profile': function (val) {
            this.config.temp.profile = val.split(' ').join('_')
        }
    },
    methods: {
        decodePath: _.debounce(function () {
            this.config.temp.modes[1].profile.stream.path = decodeURIComponent(JSON.parse('"' + this.config.temp.modes[1].profile.stream.path + '"'))
        }, 500),
        getProfiles(mode) {
            let action
            switch (mode) {
                case 0:
                    action = "get_ssh_configs"
                    break
                case 1:
                    action = "get_sshl_configs"
                    break
                case 2:
                    action = "get_sshwscdn_configs"
                    break
                case 3:
                    action = "get_sshslowdns_configs"
                    break
            }
            axios.post('api.php', {
                action: action
            }).then((res) => {
                if (res.data.data.length > 0) {
                    this.config.profiles = res.data.data
                } else {
                    this.config.profiles = ['--- Empty ---']
                }
                this.config.profile = this.config.profiles[0]
            })
        },
        getConfig() {
            this.getSystemConfig().then((res) => {
                this.config.system = res
                if (this.config.profile === '--- Empty ---') return
                switch (this.config.mode) {
                    case 0:
                        this.getSshConfig()
                        break
                    case 1:
                        this.getSshSslConfig()
                        break
                    case 2:
                        this.getSshWsCdnConfig()
                        break
                    case 3:
                        this.getSshSlowdnsConfig()
                        break
                }
                // resolve server host
                this.resolveServerHost()
            })
        },
        deleteConfig() {
            if (this.config.profile === '--- Empty ---') return
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                reverseButtons: true,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('api.php', {
                        action: "delete_config",
                        data: {
                            mode: this.config.mode,
                            profile: this.config.profile
                        }
                    }).then(() => {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Config has been removed',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        this.config.profile = ""
                        this.getProfiles(this.config.mode)
                    })
                }
            })
        },
        getSshConfig() {
            axios.post('api.php', {
                action: "get_ssh_config",
                profile: this.config.profile
            }).then((res) => {
                const temp = this.config.temp
                temp.mode = 0
                temp.profile = this.config.profile
                temp.modes[0].profile = res.data.data
            })
        },
        getSshSslConfig() {
            axios.post('api.php', {
                action: "get_sshl_config",
                profile: this.config.profile
            }).then((res) => {
                const temp = this.config.temp
                temp.mode = 1
                temp.profile = this.config.profile
                temp.modes[1].profile = res.data.data
            })
        },
        getSshWsCdnConfig() {
            axios.post('api.php', {
                action: "get_sshwscdn_config",
                profile: this.config.profile
            }).then((res) => {
                const temp = this.config.temp
                temp.mode = 2
                temp.profile = this.config.profile
                temp.modes[2].profile = res.data.data
            })
        },
        getSshSlowdnsConfig() {
            axios.post('api.php', {
                action: "get_sshslowdns_config",
                profile: this.config.profile
            }).then((res) => {
                const temp = this.config.temp
                temp.mode = 3
                temp.profile = this.config.profile
                temp.modes[3].profile = res.data.data
            })
        },
        getSystemConfig() {
            return new Promise((resolve) => {
                axios.post('api.php', {
                    action: "get_system_config"
                }).then((res) => {
                    resolve(res.data.data)
                })
            })
        },
        saveConfig() {
            const configMode = this.config.temp.mode
            const configProfile = this.config.temp.profile
            let config, title
            switch (configMode) {
                case 0:
                    config = this.config.temp.modes[0].profile
                    title = "SSH config has been saved"
                    break
                case 1:
                    config = this.config.temp.modes[1].profile
                    title = "SSH-SSL config has been saved"
                    break
                case 2:
                    config = this.config.temp.modes[2].profile
                    title = "SSH-WS-CDN config has been saved"
                    break
                case 3:
                    config = this.config.temp.modes[3].profile
                    title = "SSH-SlowDNS config has been saved"
                    break
            }
            axios.post('api.php', {
                action: "save_config",
                data: {
                    mode: configMode,
                    profile: configProfile,
                    config: config
                }
            }).then(() => {
                console.log(title)
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: title,
                    showConfirmButton: false,
                    timer: 1500
                })
                // reload config menu
                this.config.profile = ""
                this.getProfiles(this.config.mode)
            })
        },
        resolveServerHost: _.debounce(function () {
            switch (this.config.temp.mode) {
            	// ssh
                case 0:
                    axios.post('api.php', {
                        action: 'resolve_host',
                        host: this.config.temp.modes[0].profile.host
                    }).then((res) => {
                        this.config.temp.modes[0].profile.ip = res.data.data[0]
                    })
                    break
                // ssh-ssl
                case 1:
                    axios.post('api.php', {
                        action: 'resolve_host',
                        host: this.config.temp.modes[1].profile.host
                    }).then((res) => {
                        this.config.temp.modes[1].profile.ip = res.data.data[0]
                    })
                    break
                // ssh-ws-cdn
                case 2:
                    axios.post('api.php', {
                        action: 'resolve_host',
                        host: this.config.temp.modes[2].profile.host
                    }).then((res) => {
                        this.config.temp.modes[2].profile.ip = res.data.data[0]
                    })
                    axios.post('api.php', {
                        action: 'resolve_host',
                        host: this.config.temp.modes[2].profile.http.cdn.sni
                    }).then((res) => {
                        this.config.temp.modes[2].profile.http.cdn.ip = res.data.data[0]
                    })
                   break
                 // ssh-slowdns
                case 3:
                    axios.post('api.php', {
                        action: 'resolve_host',
                        host: this.config.temp.modes[3].profile.host
                    }).then((res) => {
                        this.config.temp.modes[3].profile.ip = res.data.data[0]
                    })
                    break
            }
        }, 500)
    },
    created() {
        this.config.mode = this.sortedModes[0].value
        this.config.temp.mode = this.sortedModes[0].value
        this.getProfiles(this.sortedModes[0].value)
    }
})