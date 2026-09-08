<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
          
          <div class="col-md-6">
            <div class="card" data-aos="fade-up" data-aos-delay="300">
              <div class="card-header">
                <h5 class="m-0"><?php echo trans('most-booked-sessions') ?></h5>
              </div>
              <div class="card-body">
                <div id="sessionChart"></div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card" data-aos="fade-up" data-aos-delay="350">
              <div class="card-header">
                <h5 class="m-0"><?php echo trans('most-booked-mentee') ?></h5>
              </div>
              <div class="card-body">
                <div id="menteesPie"></div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card" data-aos="fade-up" data-aos-delay="400">
              <div class="card-header">
                <h5 class="mb-0"><?php echo trans('net-income') ?></h5>
              </div>
              
              <div class="card-body">
                <div id="netIncomeChart"></div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card" data-aos="fade-up" data-aos-delay="500">
              <div class="card-header">
                <h5 class="m-0"><?php echo trans('most-booked-country') ?></h5>
              </div>
              <div class="card-body">
                <div id="countryPie"></div>
              </div>
            </div>
          </div>

          <?php if(is_admin() && !empty($mentors)): ?>
            <div class="col-md-6">
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap <?php if(is_countable($mentors) && count($mentors)  > 10){echo "datatable";} ?>">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th><?php echo trans('mentor') ?></th>
                      <th><?php echo trans('total-booking') ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i=1; foreach ($mentors as $mentor): ?>
                      <tr>
                        <td><?php echo html_escape($i); ?></td>
                        <td><?php echo html_escape($mentor->mentor_name) ?></td>
                        <td><?php echo html_escape($mentor->total) ?></td>
                      </tr>
                    <?php $i++; endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php endif; ?>

      </div>
    </div>
  </div>
</div>