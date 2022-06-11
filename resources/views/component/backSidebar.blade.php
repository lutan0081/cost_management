<a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
<i class="fas fa-bars"></i>
</a>

<!-- sidebar-wrapper  -->
<nav id="sidebar" class="sidebar-wrapper">

    <!-- sidebar-content(中断)  -->
    <div class="sidebar-content">

        <!-- サイドメニュータイトル -->
        <div class="sidebar-brand">
            <a href="#">COST Ver 1.00</a>
            <div id="close-sidebar">
                <i class="fas fa-times"></i>
            </div>
        </div>
        <!-- サイドメニュータイトル -->

        <div class="sidebar-header">

            <div class="user-pic">
            <img class="img-responsive img-rounded" src="./img/lutan_logo_64.ico" alt="User picture">
            </div>

            <div class="user-info">

                <span class="user-name">
                    <i class="bi bi-person-fill me-1"></i></i></i></i>{{ Session::get('create_user_name') }}
                </span>

                <span class="user-role">
                    {{ Session::get('permission_type_name') }}
                </span>

                <span class="user-status">
                    <i class="fa fa-circle"></i>
                    <span>Online</span>
                </span>
                
            </div>

        </div>
        <!-- sidebar-header  -->


        <!-- sidebar-menu  -->
        <div class="sidebar-menu">
            <!-- 親要素ul -->
            <ul>
                <li>
                    <a href="backHomeInit">
                        <i class="fas fa-laptop-house"></i>
                        <span>ホーム</span>
                    </a>
                </li>

                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="fas bi bi-piggy-bank-fill"></i>                  
                        <span>収支管理</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li>
                                <a href="backProfitInit">売上一覧</a>
                            </li>
                            <li>
                                <a href="backCostInit">経費一覧</a>
                            </li>
                        </ul>
                    </div>
                </li>

                @if(Session::get('permission_type_id') == 1)
                    <li class="sidebar-dropdown">
                        <a href="#">
                            <i class="fas fa-key me-2"></i>
                            <span>マスタ</span>
                            <span class="badge badge-pill badge-danger"></span>
                        </a>

                        <div class="sidebar-submenu">
                            <ul>
                                <li>
                                    <a href="backOwnerInit">家主一覧</a>
                                </li>
                                <li>
                                    <a href="backRealEstateInit">物件一覧</a>
                                </li>
                                <li>
                                    <a href="backRoomInit">部屋一覧</a>
                                </li>
                                <li>
                                    <a href="backBankInit">銀行一覧</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if(Session::get('permission_type_id') == 1)
                    <li class="sidebar-dropdown">
                        <a href="#">
                            <i class="far fa-gem"></i>
                            <span>設定</span>
                            <span class="badge badge-pill badge-danger"></span>
                        </a>
                        <div class="sidebar-submenu">
                            <ul>
                                <li>
                                    <a href="backUserInit">ユーザ</a>
                                </li>
                                <li>
                                    <a href="backUserInit">勘定科目</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

            </ul>
            <!-- 親要素ul -->
        </div>
        <!-- sidebar-menu  -->

    </div>

    <!-- sidebar-content(下段)  -->
    <div class="sidebar-footer">

        <!-- お知らせ -->
        <a href="#">
            <i class="fa fa-bell"></i>
            <span class="position-absolute top-0 start-90 badge rounded-pill bg-warning text-dark">3</span>
        </a>

        <!-- メッセージ -->
        <a href="#">
            <i class="fa fa-envelope"></i>
            <span class="position-absolute top-0 start-90 badge rounded-pill bg-success">7</span>
        </a>

        <!-- 設定 -->
        <a href="#">
            <i class="fa fa-cog"></i>
            <span class="badge-sonar"></span>
        </a>

        <!-- ログアウト -->
        <a href="logOut">
            <i class="fa fa-power-off"></i>
        </a>

    </div>

</nav>
<!-- sidebar-wrapper  -->