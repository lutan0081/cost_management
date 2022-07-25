<!DOCTYPE html>
<html lang="ja">

	<head>
		<title>Home/COST</title>

		<!-- head -->
        @component('component.backHead')
        @endcomponent

		<!-- 自作css -->
		<link rel="stylesheet" href="{{ asset('back/css/back_home.css') }}">  
		
        <style>
            /* ボタンデフォルト値 */
            .btn-default{
                width: 5rem;
            }
		</style>
        
	</head>

	<body>
		<!-- page-wrapper -->
		<div class="page-wrapper chiller-theme toggled">

		<!-- ローディング画面の表示 -->
		<div id="overlay">
			<div class="cv-spinner">
				<span class="spinner"></span>
			</div>
		</div>
		
		<!-- sidebar-wrapper  -->
		@component('component.backSidebar')
		@endcomponent
		<!-- sidebar-wrapper  -->
		
		<!-- page-content" -->
		<main class="page-content">

			<!-- ダッシュボード -->
			<div class="container">

                <div class="info_title mt-3">
                    <i class="bi bi-hourglass-split icon_blue me-2"></i>Dashboard
                </div>
            
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12 mt-3">

                        <div class="row">

                            <!-- ボックス1  -->
                            <div class="col-12 col-md-12 col-lg-3 dashboard_box">
                                <div class="row">
                                    <!-- 子要素cssで95%に設定し、mx-autoで中央に配置 -->
                                    <div class="col-12 col-md-12 col-lg-12 dashboard_box_inner_1 mx-auto">
                                        <div class="row">

                                            <div class="col-12 col-md-12 col-lg-12 pt-5">
                                                <span class="dashboard_box_title">
                                                    <i class="fas bi bi-piggy-bank me-2"></i>売上
                                                </span> 
                                            </div>
                                            
                                            <div class="col-12 col-md-12 col-lg-12 pt-2">
                                                月間: <span class="count dashboard_box_num">{{ Common::format_three_digit_separator($thisMonthProfit_list->profit_fee) }}</span><span class="ms-1">円</span>
                                            </div>

                                            <div class="col-12 col-md-12 col-lg-12">
                                                年間: <span class="count dashboard_box_num">{{ Common::format_three_digit_separator($thisYearProfit_list->profit_fee) }}</span><span class="ms-1">円</span>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ボックス1 -->

                            <!-- ボックス2 -->
                            <div class="col-12 col-md-12 col-lg-3 dashboard_box">
                                <div class="row">
                                    <!-- 子要素cssで95%に設定し、mx-autoで中央に配置 -->
                                    <div class="col-12 col-md-12 col-lg-12 dashboard_box_inner_2 mx-auto">
                                        <div class="row">

                                            <div class="col-12 col-md-12 col-lg-12 pt-5">
                                                <span class="dashboard_box_title">
                                                <i class="bi bi-wallet2"></i>
                                                    経費
                                                </span> 
                                            </div>
                                            
                                            <div class="col-12 col-md-12 col-lg-12 pt-2">
                                                月間: <span class="count dashboard_box_num">{{ Common::format_three_digit_separator($thisMonthCost_list->outgo_fee) }}</span><span class="ms-1">円</span>
                                            </div>

                                            <div class="col-12 col-md-12 col-lg-12">
                                                年間: <span class="count dashboard_box_num">{{ Common::format_three_digit_separator($thisYearCost_list->outgo_fee) }}</span><span class="ms-1">円</span>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ボックス2  -->

                            <!-- ボックス3 -->
                            <div class="col-12 col-md-12 col-lg-3 dashboard_box">
                                <div class="row">
                                    <!-- 子要素cssで95%に設定し、mx-autoで中央に配置 -->
                                    <div class="col-12 col-md-12 col-lg-12 dashboard_box_inner_3 mx-auto">
                                        <div class="row">

                                            <div class="col-12 col-md-12 col-lg-12 pt-5">
                                                <span class="dashboard_box_title">
                                                    <i class="bi bi-hand-thumbs-up"></i>
                                                    未承諾
                                                </span> 
                                            </div>
                                            
                                            <div class="col-12 col-md-12 col-lg-12 pt-2">
                                                売上: <span class="count dashboard_box_num">{{ Common::format_three_digit_separator($profitApproval_list->row_count) }}</span><span class="ms-1">件</span>
                                            </div>
                                        
                                            <div class="col-12 col-md-12 col-lg-12">
                                                経費: <span class="count dashboard_box_num">{{ Common::format_three_digit_separator($costApproval_list->row_count) }}</span><span class="ms-1">件</span>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ボックス3 -->
                        
                            <!-- ボックス4 -->
                            <div class="col-12 col-md-12 col-lg-3 buruburu dashboard_box">
                                <div class="row">
                                    <!-- 子要素cssで95%に設定し、mx-autoで中央に配置 -->
                                    <div class="col-12 col-md-12 col-lg-12 dashboard_box_inner_4 mx-auto">
                                        <div class="row">
                                            <div class="col-12 col-md-12 col-lg-12 pt-5">
                                                <span class="dashboard_box_title">
                                                    <i class="bi bi-question-circle"></i>
                                                    Q&A
                                                </span> 
                                            </div>
                                            
                                            <div class="col-12 col-md-12 col-lg-12 pt-2">
                                                売上: <span class="count dashboard_box_num">{{ Common::format_three_digit_separator($profit_quetion_list->row_count) }}</span><span class="ms-1">件</span>
                                            </div>
                                        
                                            <div class="col-12 col-md-12 col-lg-12">
                                                経費: <span class="count dashboard_box_num">{{ Common::format_three_digit_separator($cost_quetion_list->row_count) }}</span><span class="ms-1">件</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ボックス4 -->

                            <!-- ボックス5 -->
                            <div class="col-12 col-md-12 col-lg-3 dashboard_box">
                                <div class="row">
                                    <!-- 子要素cssで95%に設定し、mx-autoで中央に配置 -->
                                    <div class="col-12 col-md-12 col-lg-12 dashboard_box_inner_5 mx-auto">
                                        <div class="row">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ボックス5 -->

                            <!-- ボックス6 -->
                            <div class="col-12 col-md-12 col-lg-3 dashboard_box">
                                <div class="row">
                                    <!-- 子要素cssで95%に設定し、mx-autoで中央に配置 -->
                                    <div class="col-12 col-md-12 col-lg-12 dashboard_box_inner_6 mx-auto">
                                        <div class="row">           
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ボックス6 -->

                            <!-- ボックス7 -->
                            <div class="col-12 col-md-12 col-lg-3 dashboard_box">
                                <div class="row">
                                    <!-- 子要素cssで95%に設定し、mx-autoで中央に配置 -->
                                    <div class="col-12 col-md-12 col-lg-12 dashboard_box_inner_7 mx-auto">
                                        <div class="row">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ボックス7 -->

                            <!-- ボックス8 -->
                            <div class="col-12 col-md-12 col-lg-3 dashboard_box">
                                <div class="row">
                                    <!-- 子要素cssで95%に設定し、mx-autoで中央に配置 -->
                                    <div class="col-12 col-md-12 col-lg-12 dashboard_box_inner_8 mx-auto">
                                        <div class="row">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ボックス8 -->

                        </div>
                    </div>
                </div>
			</div>
			<!-- ダッシュボード -->

            <!-- 売上グラフ -->
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12 mt-3">
                        <div class="row">

                            <div class="col-12 col-md-12 col-lg-12">
                                <canvas id="myChart"></canvas>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- 売上グラフ -->

			<!-- お知らせ -->
			<div class="container mb-3">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="overflow-auto" style="max-height:30rem;">
                            <div class="table-responsive">
                                
                                <span class="info_title">
                                    <i class="bi bi-bell-fill icon_blue me-2"></i>Information
                                </span>
                                <hr class="info_hr">

                                <table class="table table-hover table-condensed">
                                    
                                    <thead>
                                        <tr>
                                            <th scope="col" style="display:none">id</th>
                                            <th><i class="bi bi-check2-square"></i></th>
                                            <th scope="col">タイトル</th>
                                            <th scope="col">内容</th>
                                            <th scope="col">登録・更新日</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($res as $information_list)
                                            <tr>
                                                <td td id="id_{{ $information_list->information_id }}" class="click_class" style="display:none"></td>
                                                <td td id="cb_{{ $information_list->information_id }}" class="click_class"><input id="{{ $information_list->information_id }}" class="form-check-input btn_radio" type="radio" name="flexRadioDisabled"></td>
                                                <td td id="title_{{ $information_list->information_id }}" class="click_class">{{ $information_list->information_name }}</td>
                                                <td td id="contents_{{ $information_list->information_id }}" class="click_class">{{ $information_list->information_type_name }}</td>
                                                <td td id="date_{{ $information_list->information_id }}" class="click_class">{{ $information_list->information_contents }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ぺージネーション -->   
                    <div id="links" style="display:none;" class="mt-2">
                        {{ $res->appends($paginate_params)->links() }}
                    </div>

                </div>
            </div>

		</main>
		<!-- page-content" -->

		</div>
		<!-- page-wrapper -->

        <!-- js -->
        @component('component.backJs')
        @endcomponent

		<!-- 自作js -->
		<script src="{{ asset('back/js/back_home.js') }}"></script>
        
        <!-- グラフjs -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <script>
            const ctx = document.getElementById('myChart');
            const myChart = new Chart(ctx, {
                type: 'bar',
                data: {

                    // 年月データ
                    labels: [
                        @foreach($date_list as $value)
                            '{{ $value }}',
                        @endforeach
                    ],

                    // 売上数値
                    datasets: [{
                        label: '売上高',
                        data: [
                            @foreach($money_list as $value)
                                '{{ $value }}',
                            @endforeach
                        ],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(201, 203, 207, 0.2)'
                            ],
                            borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(54, 162, 235)',
                            'rgb(153, 102, 255)',
                            'rgb(201, 203, 207)'
                            ],

                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: '年月'
                            },
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: '万円'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>

	</body>
	
</html>