    @extends('main')
    @section('main')

      <section class="section">
        <div class="container">
          <div id="jjson" class="jjson" style="display: none;"></div>
        </div>
      </section>

      <section class="section">
        <div class="container">        
          <div class="tile is-ancestor">
            <div class="tile is-5 is-parent">
                <article class="tile notirication is-child">
                  <p class="title">交易实况</p><span id="last-updated" hidden>{{ $lastupdated }}</span>                  
                  <hr>
                  <div class="event-table" id="events">
                    @foreach($events as $event)
                      <div class="event-element" id="{{'el-' . $event->eid}}">
                        <span class="event-time">{{$event->updated_at}}</span><span class="event-event">{!! $event->event !!}</span>
                      </div>
                      <!--
                      <div class="event-element" id="">
                        <span class="event-time">2017-10-2 19:40:32</span><span class="event-event">rainbow6以价格<span class="event-buyingprice">4.56</span><span class="event-buy">买入</span>200股<span class="event-sharename">东方不败</span>
                      </div>
                      <div class="event-element">
                        <span class="event-time">2017-10-8 3:41:05</span><span class="event-event">rainbow6卖出200股中国电信</span>
                      </div> 
                      -->
                    @endforeach                       
                  </div>
                </article>
            </div>            
            

            <div class="tile is-7 is-parent">
                <article class="tile is-child">
                    <p class="title" id="index-info"><span id="title-for-chart">
                    @if(isset($index) && isset($config))
                      {{ $index[$config['default_index']]['name'] }}
                    @else
                      上证指数
                    @endif
                    </span>：<span class="stock-detail" id="curr_index"></span>  <span class="stock-detail" id="change_in_points"></span>  <span class="stock-detail" id="change_in_percent"></span>  <span class="stock-detail" id="transaction_volume"></span> <span class="stock-detail" id="transaction_amount"></span></p>
                    <nav class="breadcrumb is-centered" aria-label="大盘指数" id="chart-nav">
                      <ul>
                        @if(empty($index))
                          <li class="is-active"><a aria-current="page" id="sh-chart">上证</a></li>
                          <li><a id="sz-chart">深证</a></li>
                          <li><a id="dow-chart">Dow Jones</a></li>
                          <li><a id="nas-chart">NASDAQ</a></li>
                        @else
                          @foreach($index as $key => $idata)
                              @if($key == $config['default_index'])
                                <li class="is-active"><a aria-current="page" id="{{$key . '-chart'}}">{{$idata['name']}}</a></li>
                              @elseif ($idata['status'])
                                <li><a id="{{$key . '-chart'}}">{{$idata['name']}}</a></li>
                              @endif
                          @endforeach
                        @endif
                      </ul>                      
                    </nav>
                    <figure class="image is-545*300" id="chart">
                        @if(isset($index) && isset($config))
                          @if($index[$config['default_index']]['status'])
                              <img src="{{ $index[$config['default_index']]['min_chart_url'] }}">
                          @else
                              <img src="{{ asset('images/noimage.jpg') }}">
                          @endif
                        @else
                          <img src="http://image.sinajs.cn/newchart/min/n/sh000001.gif">
                        @endif
                    </figure>
                </article>
            </div>    

          </div>
         
        </div>
      </section>
    @stop

    @section('scripts')
      <script src="{{asset('js/home.js')}}"></script>  
    @stop