/**
 * React User class
 * Showing list of users
 * Get single user and show in popup element
 */
class Users extends React.Component {
    /**
     * Constructor
     * @param {*} props 
     */
    constructor(props) {
        super(props);
        this.state = {
            loading: 0,
            loaded: 0,
            user: false,
            usersCache: {}
        };
        this.showDetails = this.showDetails.bind(this);
        this.closeDetails = this.closeDetails.bind(this);
    }

    /**
     * Load and show user details
     * @param {*} e 
     */
    showDetails(e) {
        e.preventDefault();
        if(!this.state.loading) {
            let target = e.target;
            let id = target.getAttribute('data-id');
            let usersCache = this.state.usersCache;
            if(usersCache.hasOwnProperty(id)) {
                this.setState({
                    loading: 0,
                    loaded: id,
                    user: usersCache[id]
                });
            } else {
                this.setState({
                    loading: id
                }, () => {
                    let xhttp = new XMLHttpRequest();
                    let react = this;
                    xhttp.onreadystatechange = function() {
                        if (this.readyState == 4) {
                            react.setState({
                                loading: 0
                            }, () => {
                                if(this.status == 200) {
                                    let response = JSON.parse(this.responseText);
                                    if(response.code==200) {
                                        let user = response.user;
                                        if(!usersCache.hasOwnProperty(user.id)) {
                                            usersCache[user.id] = user;
                                        }
                                        react.setState({
                                            loading: 0,
                                            loaded: user.id,
                                            user: user,
                                            usersCache: usersCache
                                        });
                                    } else {
                                        react.setState({
                                            loading: 0,
                                            loaded: 0,
                                            user: false
                                        }, () => {
                                            alert(response.message);
                                        });
                                    }
                                } else {
                                    react.setState({
                                        loading: 0,
                                        loaded: 0,
                                        user: false
                                    }, () => {
                                        alert('Failed to load user, please try again');
                                    });
                                }
                            });
                        }
                    };
                    xhttp.open("GET", target.getAttribute('href')+'&type=json', true);
                    xhttp.send();
                });
            }
        } else {
            //console.log('still loading...')
        }
    }

    /**
     * Close user details
     * @param {*} e 
     */
    closeDetails(e) {
        e.preventDefault();
        this.setState({
            loading: 0,
            loaded: 0,
            user: false
        });
    }

    /**
     * Render
     * @returns void
     */
    render() {
        let sep = this.props.sep;
        let UsersHeadElms = null;
        let UsersElms = null;
        if(this.props.users.length>0) {
            UsersHeadElms = React.createElement('div', {key: 'rowhead', className: 'row head'}, [
                React.createElement('div', {key: 'colhead1', className: 'cell user-id'}, 'ID'),
                React.createElement('div', {key: 'colhead2', className: 'cell user-name'}, 'Name'),
                React.createElement('div', {key: 'colhead3', className: 'cell user-username'}, 'Username'),
                React.createElement('div', {key: 'colhead4', className: 'cell user-email'}, 'Email')
            ]);
            UsersElms = this.props.users.map((value, i) => {
                return React.createElement('div', {
                    key: 'userrow'+i,
                    id: value.id,
                    'data-id': value.id,
                    className: 'row'
                }, [
                    React.createElement('div', {key: 'user'+i+'col1', className: 'cell user-id'}, 
                        React.createElement('a', {
                            key: 'user'+i+'id',
                            href: sep+'id='+value.id,
                            'data-id': value.id,
                            onClick: this.showDetails
                        }, value.id)
                    ),
                    React.createElement('div', {key: 'user'+i+'col2', className: 'cell user-name'}, [
                        React.createElement('a', {
                            key: 'user'+i+'name',
                            href: sep+'id='+value.id,
                            'data-id': value.id,
                            onClick: this.showDetails
                        }, value.name)
                    ]),
                    React.createElement('div', {key: 'user'+i+'col3', className: 'cell user-username'}, 
                        React.createElement('a', {
                            key: 'user'+i+'username',
                            href: sep+'id='+value.id,
                            'data-id': value.id,
                            onClick: this.showDetails
                        }, value.username)
                    ),
                    React.createElement('div', {key: 'user'+i+'col4', className: 'cell user-email'}, 
                        React.createElement('a', {
                            key: 'user'+i+'email',
                            href: sep+'id='+value.id,
                            'data-id': value.id,
                            onClick: this.showDetails
                        }, value.email)
                    ),
                ]);
            });
        } else {
            let message = 'No users yet';
            if(this.props.hasOwnProperty('message') && this.props.message!=='') {
                message = this.props.message;
            }
            UsersElms = React.createElement('div', {key: 'rowhead', className: 'row'}, [
                React.createElement('div', {key: 'colhead1', className: 'cell'}, message)
            ]);
        }
        let UserElm = null;
        if(this.state.loaded>0 && this.state.hasOwnProperty('user') && this.state.user) {
            let id = this.state.loaded;
            let details = [
                {name: 'ID', slug: 'id'}, 
                {name: 'Name', slug: 'name'}, 
                {name: 'Username', slug: 'username'}, 
                {name: 'Email', slug: 'email'},
                {name: 'Address', slug: 'fullAddress'}, 
                {name: 'Phone', slug: 'phone'}, 
                {name: 'Website', slug: 'website'}, 
                {name: 'Company', slug: 'companyName'}
            ];
            let user = this.state.user;
            let userData = user;
            let address = user.hasOwnProperty('address')?user.address:{};
            let street = address.hasOwnProperty('street')?address.street:'';
            let suite = address.hasOwnProperty('suite')?address.suite:'';
            let city = address.hasOwnProperty('city')?address.city:'';
            let zipcode = address.hasOwnProperty('zipcode')?address.zipcode:'';
            userData.fullAddress = street+' '+suite+' '+city+' '+zipcode;
            let company = user.hasOwnProperty('company')?user.company:{};
            userData.companyName = company.hasOwnProperty('name')?company.name:'';
            let DetailsElm = details.map((value, i) => {
                return React.createElement('div', {
                    key: 'userrow'+value,
                    className: 'row'
                }, [
                    React.createElement('div', 
                        {key: 'user'+id+value+'label', className: 'cell col-label'}, 
                        value.name
                    ),
                    React.createElement('div', 
                        {key: 'user'+id+value+'content', className: 'cell col-content'}, 
                        userData[value.slug]
                    )
                ]);
            });
            let top = document.getElementById('users').offsetTop+40;
            UserElm = React.createElement('div', {
                key: 'user'+id+'container', 
                className: 'fly-container',
                style: {top: top+'px'}
            }, [
                React.createElement('a', {
                    key: 'user'+id+'close',
                    className: 'btn btn-primary btn-close',
                    href: this.props.uri,
                    onClick: this.closeDetails
                }, 'Close'),
                React.createElement('div', {key: 'user'+id+'table', className: 'table'}, DetailsElm)
            ]);
        }
        let tableClass = (this.state.loading>0)?' loading':'';
        let LoadingElm = null;
        if(this.state.loading>0) {
            LoadingElm = React.createElement('div', {key: 'loader', className: 'lds-ellipsis'}, [
                React.createElement('div', {key: 'loader1'}),
                React.createElement('div', {key: 'loader2'}),
                React.createElement('div', {key: 'loader3'}),
                React.createElement('div', {key: 'loader4'})
            ]);
        }
        return React.createElement('div', null, [
            LoadingElm,
            React.createElement('div', {key: 'userstable', className: 'table'+tableClass}, [
                UsersHeadElms,
                UsersElms
            ]),
            UserElm
        ]);
    }
}

// Run react
ReactDOM.render (
    React.createElement(Users, {users: users, url: url, uri: uri, sep: sep, message: message}, null),
    document.getElementById('users')
);