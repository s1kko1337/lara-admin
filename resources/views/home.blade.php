@extends('layout')

@section('content')
<div class="container" style="margin-top: 15px;">
    <div class="row">

    <div class="col-12 col-md-6">
            <a href="{{ route('user.chats.index') }}" class="card mb-4" style="text-decoration: none; color: inherit;">
                    <div class="card-body text-center">
                        <h5 class="card-title">Количество обращений (чатов)</h5>
                        <p class="display-4">{{$uniqueIdCount}}</p>
                         <div class="row">
                         <h5 class="card-title">Активные чаты</h5>
                         <p>{{$uniqueIdCountActive}}</p>
                         <h5 class="card-title">Неактивные чаты</h5>
                        <p>{{$uniqueIdCountInactive}}</p>
                         </div>
                    </div>
            </a>
        </div>

        

        <div class="col-12 col-md-6">
        <div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Статистика</h5>
        <div class="form-group">
            <label for="statType">Выберите тип статистики:</label>
            <select id="statType" class="form-control">
                <option value="models">Просмотры по моделям</option>
                <option value="chats" selected>Статистика чатов</option>
                <option value="profile">Просмотры профиля</option>
            </select>
        </div>
        <div class="form-group">
            <label for="timeRange">Выберите промежуток времени:</label>
            <select id="timeRange" class="form-control">
                <option value="day">День</option>
                <option value="week">Неделя</option>
                <option value="month">Месяц</option>
                <option value="all" selected>Все время</option>
            </select>
        </div>
        <div id="statsChart" style="width: 100%; height: 400px;"></div>
    </div>
</div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Статус сервера</h5>
                    @if($ftpAvailable)
                        <p class="text-success">Сервер доступен, можно загружать файлы.</p>
                    @else
                        <p class="text-danger">Сервер недоступен.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="{{route('user.file.get') }}" class="nav-link" style="text-decoration: none; color: inherit;">
                            Загруженные модели на сервер
                        </a>
                    </h5>
                    <ul class="list-group">
                        @foreach($models as $model)
                            <li class="list-group-item">
                                <a href="{{ route('user.download.model', ['id' => $model->id]) }}" class="nav-link">
                                    {{ $model->model_name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.1/echarts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var chartDom = document.getElementById('statsChart');
    var myChart = echarts.init(chartDom);
    var timeRangeSelect = document.getElementById('timeRange');
    var statTypeSelect = document.getElementById('statType');

    function fetchDataAndRenderChart() {
        var timeRange = timeRangeSelect.value;
        var statType = statTypeSelect.value;

        fetch('{{ route('user.chat.stats') }}?timeRange=' + timeRange + '&statType=' + statType)
            .then(response => response.json())
            .then(data => {
                var option = {};

                if (statType === 'chats') {
                    var chartData = data.map(item => {
                        return { value: item.message_count, name: 'Чат ID ' + item.chat_id };
                    });

                    option = {
                        title: {
                            text: 'Количество сообщений по чатам',
                            left: 'center',
                            textStyle: {
                                fontSize: window.innerWidth < 768 ? 14 : 18,
                                color: '#333'
                            }
                        },
                        tooltip: {
                            trigger: 'item',
                            formatter: '{b}: {c} сообщений ({d}%)'
                        },
                        legend: {
                            orient: 'vertical',
                            left: 'left',
                            top: 'middle',
                            data: chartData.map(item => item.name)
                        },
                        series: [
                            {
                                name: 'Количество сообщений',
                                type: 'pie',
                                radius: window.innerWidth < 768 ? '50%' : '55%',
                                center: ['50%', '50%'],
                                data: chartData,
                                emphasis: {
                                    itemStyle: {
                                        shadowBlur: 10,
                                        shadowOffsetX: 0,
                                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                                    }
                                }
                            }
                        ]
                    };
                } else if (statType === 'models') {
                    var chartData = data.map(item => {
                        return { value: item.views, name: item.model_name };
                    });

                    option = {
                        title: {
                            text: 'Просмотры по моделям',
                            left: 'center',
                            textStyle: {
                                fontSize: window.innerWidth < 768 ? 14 : 18,
                                color: '#333'
                            }
                        },
                        tooltip: {
                            trigger: 'item',
                            formatter: '{b}: {c} просмотров ({d}%)'
                        },
                        legend: {
                            orient: 'vertical',
                            left: 'left',
                            top: 'middle',
                            data: chartData.map(item => item.name)
                        },
                        series: [
                            {
                                name: 'Просмотры',
                                type: 'pie',
                                radius: window.innerWidth < 768 ? '50%' : '55%',
                                center: ['50%', '50%'],
                                data: chartData,
                                emphasis: {
                                    itemStyle: {
                                        shadowBlur: 10,
                                        shadowOffsetX: 0,
                                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                                    }
                                }
                            }
                        ]
                    };
                } else if (statType === 'profile') {
                    var totalViews = data.value.map(item => item.views);

// Общий итог просмотров
var totalViewsCount = totalViews.reduce((acc, curr) => acc + curr, 0);
                    option = {
        title: {
            text: 'Просмотры профиля',
            left: 'center',
            textStyle: {
                fontSize: window.innerWidth < 768 ? 14 : 18,
                color: '#333'
            }
        },
        series: [
            {
                name: 'Просмотры',
                type: 'pie',
                radius: ['50%', '70%'], 
                avoidLabelOverlap: false,
                label: {
                    show: false,
                    position: 'center'
                },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: '24',
                        fontWeight: 'bold',
                        formatter: '{c}' // Показывает значение
                    }
                },
                labelLine: {
                    show: false
                },
                data: [
                    { value: totalViewsCount, name: 'Просмотры профиля' }
                ]
            }
        ],
        tooltip: {
            show: true,
            formatter: '{b}: {c} ({d}%)' // Информация о разделе
        },
        graphic: {
            type: 'text',
            left: 'center',
            top: 'center',
            style: {
                text: `${totalViewsCount}`, // Преобразование общего количества просмотров в строку
                textAlign: 'center',
                fill: '#000',
                fontSize: 40,
                fontWeight: 'bold'
            }
        }
                    };
                }

                myChart.setOption(option);
            });
    }

    fetchDataAndRenderChart();

    function updateTimeRangeVisibility() {
        if (statTypeSelect.value === 'profile') {
            timeRangeSelect.style.display = 'none';
        } else {
            timeRangeSelect.style.display = 'block';
        }
    }

    timeRangeSelect.addEventListener('change', fetchDataAndRenderChart);
    statTypeSelect.addEventListener('change', function () {
        updateTimeRangeVisibility();
        fetchDataAndRenderChart();
    });

    updateTimeRangeVisibility();
});
</script>
@endsection