'use strict';

function Login() {
    this.isLoginPageRendered = false;
    this.isLogin = false;

    this.qbConnect = function (data) {

        var
            self = this,
            timer,
            userRequiredParams = {
                'login': data.login,
                'password': data.password ? data.password : 'quickblox'
            };

        this.login = function() {
            return new Promise(function (resolve, reject) {
                QB.login(userRequiredParams, function (error, result) {
                    if (error) {
                        reject(error);
                    } else {
                        resolve(result);
                    }
                });
            });
        };

        this.userCreate = function(user) {
            return new Promise(function (resolve, reject) {
                QB.users.create(user, function (error, result) {
                    if (error) {
                        reject(error);
                    } else {
                        resolve(result);
                    }
                });
            });
        };

        this.createSession = function() {
            return new Promise(function(resolve, reject) {
                QB.createSession(function(error, result) {
                    if(error) {
                        reject(error);
                    } else {
                        resolve(result);
                    }
                });
            });
        };

        this.chatConnect = function() {
            return new Promise(function(resolve, reject) {
                QB.chat.connect({
                    jid: QB.chat.helpers.getUserJid( app.user.id, app._config.credentials.appId ),
                    password: userRequiredParams.password
                }, function(error, result) {
                    if(error) {
                        reject(error);
                    } else {
                        resolve(result)
                    }
                });
            });
        };

        

    }

}

Register.prototype.init = async function() {
    var self = this;

    var user = localStorage.getItem('user');

    if(!app.checkInternetConnection()){
        return false;
    }

    if(user && !app.user){
        var savedUser = JSON.parse(user);
        app.room = savedUser.tag_list;
        return await self.login(savedUser);
    }

    return Promise.resolve(false);

};

Register.prototype.login = async function (user) {
    var self = this;

    window.qbConnect = new self.qbConnect(user);

    var session = await window.qbConnect.createSession();

    app.token = session.token;

    try {
        var userData = await window.qbConnect.login();
    }catch (e) {
        await userModule.create(user);
        userData = await window.qbConnect.login();
    }

    if(userData.user_tags !== user.tag_list || userData.full_name !== user.full_name) {
        userData = await userModule.update(userData.id,{
            'full_name': user.full_name,
            'tag_list': user.tag_list
        });
    }
    
    app.user = userModule.addToCache(userData);
    app.user.user_tags = userData.user_tags;

    await window.qbConnect.chatConnect().then(function () {
        self.isLogin = true;
    });

    window.qbConnect.reconnecting(1800000);

    return Promise.resolve(true);

};

Register.prototype.renderRegisterPage = function(){
    helpers.clearView(app.page);

    this.isRegisterPageRendered = true;
    this.setListeners();
};



Login.prototype.setListeners = function(){
    var self = this,
        registerForm = document.forms.registerForm,
        formInputs = [registerForm.email, registerForm.fname, registerForm.lname, registerForm.username, registerForm.password],
        registerBtn = registerForm.register;

    registerForm.addEventListener('submit', function(e){
        e.preventDefault();

        if(
            !app.checkInternetConnection() ||
            registerForm.hasAttribute('disabled') ||
            !registerForm.userName.isValid ||
            !registerForm.userLogin.isValid) {
            return false;
        } else {
            registerForm.setAttribute('disabled', true);
        }

        var email = registerForm.email.value,
            full_name= registerForm.fname.value+" "+registerForm.lname.value,
            login= registerForm.username.value,
            password = registerForm.password.value;

        var user = {
            email: email,
            full_name: full_name,
            login: login,
            password: password
        };

        localStorage.setItem('user', JSON.stringify(user));

        self.login(user).then(function(){
            // router.navigate('/dashboard');
        }).catch(function(error){
            alert('lOGIN ERROR\n open console to get more info');
            registerBtn.removeAttribute('disabled');
            console.error(error);
            registerForm.login_submit.innerText = 'LOGIN';
        });
    });

    // add event listeners for each input;
    _.each(formInputs, function(i){
        i.addEventListener('focus', function(e){
            if(e.target.isValid){
                e.target.nextElementSibling.classList.remove('filled');
            }else{
                e.target.nextElementSibling.classList.add('filled');
            }
        });

        i.addEventListener('focusout', function(e){
            var elem = e.currentTarget;
            if (!elem.value.length || elem.isValid) {
                elem.nextElementSibling.classList.remove('filled');
            }
        });

        i.addEventListener('input', function(e){
            

            if(e.target.isValid){
                e.target.nextElementSibling.classList.remove('filled');
            }else{
                e.target.nextElementSibling.classList.add('filled');
            }

            if(registerForm.userName.isValid && loginForm.userLogin.isValid){
                registerBtn.removeAttribute('disabled');
            }else{
                registerBtn.setAttribute('disabled', true);
            }

        })
    });
};

var loginModule = new Login();