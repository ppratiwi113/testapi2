<div class="ibox float-e-margins">
    <div class="ibox-title">
      <h5><i class="fa fa-picture-o"></i> Sebaran SDM Berdasarkan Provinsi</h5>
    </div>
    <div class="ibox-content text-center">
      <div id="map-sebaran-sdm"></div> 
    </div>
  </div>
  
  @push('javascript')
  <script type="text/javascript" src="{{asset('assets/highcharts/highmaps.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/highcharts/modules/map.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/highcharts/id-all.js')}}"></script>
  <script type="text/javascript">
      var token = $("meta[name=csrf-token]").attr("content");  
  
      var provinsi = [];
      @foreach($provinsi as $r)
          provinsi['{{ $r->id_wil }}'] = '{{ $r->nm_wil }}';
      @endforeach
  
      var listProv = {};
      listProv['01'] = 'id-jk';
      listProv['02'] = 'id-jr';
      listProv['03'] = 'id-jt';
      listProv['04'] = 'id-yo';
      listProv['05'] = 'id-ji';
      listProv['06'] = 'id-ac';
      listProv['07'] = 'id-su';
      listProv['08'] = 'id-sb';
      listProv['09'] = 'id-ri';
      listProv['10'] = 'id-ja';
      listProv['11'] = 'id-sl';
      listProv['12'] = 'id-1024';
      listProv['13'] = 'id-kb';
      listProv['14'] = 'id-kt';
      listProv['15'] = 'id-ks';
      listProv['16'] = 'id-ki';
      listProv['17'] = 'id-sw';
      listProv['18'] = 'id-st';
      listProv['19'] = 'id-se';
      listProv['20'] = 'id-sg';
      listProv['21'] = 'id-ma';
      listProv['22'] = 'id-ba';
      listProv['23'] = 'id-nb';
      listProv['24'] = 'id-nt';
      listProv['25'] = 'id-pa';
      listProv['26'] = 'id-be';
      listProv['27'] = 'id-la';
      listProv['28'] = 'id-bt';
      listProv['29'] = 'id-bb';
      listProv['30'] = 'id-go';
      listProv['31'] = 'id-kr';
      listProv['32'] = 'id-ib';
      listProv['33'] = 'id-sr';
      listProv['34'] = 'id-ku';
      listProv['99'] = 'id-';
  
      var data = [
          {
              "hc-key": "id-3700",
              "value": 0
          },
          @foreach($result['jumlahDosenProv'] as $r)
          {
              "hc-key": listProv["{{ $r['id'] }}"],
              "value": {{ (int)$r['total'] }}
          },
         @endforeach 
      ];
  
      $('#map-sebaran-sdm').highcharts('Map', {
          title : {
              text : 'Sebaran Dosen Aktif'
          },
          subtitle : {
              text : 'Nasional'
          },
          credits: {
              enabled: false
          },
          mapNavigation: {
              enabled: true,
              buttonOptions: {
                  verticalAlign: 'bottom'
              }
          },
          colorAxis: {
              min: 0,
          },
          plotOptions:{
            series:{
                point:{
                    events:{
                        click: function(){
                            id = this.properties['hc-key'];
                            var idwilayah = '';
                            $.each( listProv, function( index, value )
                            {
                              if(value==id)
                                idwilayah = index;
                            });
                            reloadChart(idwilayah, provinsi[idwilayah],'chartProvinsi',true);
                        }
                    }
                }
            }
          },
  
          series : [{
              data : data,
              mapData: Highcharts.maps['countries/id/id-all'],
              joinBy: 'hc-key',
              name: 'Jumlah Dosen',
              states: {
                  hover: {
                      color: '#BADA55'
                  }
              },
              dataLabels: {
                  enabled: true,
                  format: '{point.name}'
              }
          }]
      });
  
  </script>
  @endPush