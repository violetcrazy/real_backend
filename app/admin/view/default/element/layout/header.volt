{% set userSession = session.get('USER') %}

<div class="navbar navbar-inverse">
    <div class="container">
        <div class="navbar-header">
            <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
                <span class="clip-list-2"></span>
            </button>
            <a href="{{ url({'for': 'home'}) }}" class="navbar-brand">
                <img src="{{ config.asset.backend_url ~ 'img/logo.png?' ~ config.asset.version }}" style="height: 40px;" />
            </a>
        </div>

        <div class="navbar-tools" style="border-bottom: none;">
            <ul class="nav navbar-right">
                <li class="dropdown current-user">
                    <a href="#" data-toggle="dropdown" data-hover="dropdown" class="dropdown-toggle" data-close-others="true">
                        <img src="{{ config.asset.backend_url ~ 'img/avatar_default_small.png' }}" class="circle-img" alt="Avatar">
                        <span class="username">{{ userSession['name'] }}</span>
                        <i class="clip-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ url({'for': 'user_profile'}) }}">
                                <i class="clip-user-2"></i>
                                Tài khoản
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="{{ url({'for': 'user_logout'}) }}">
                                <i class="clip-exit"></i>
                                Thoát
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>