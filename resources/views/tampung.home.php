<form role="form">
					<input type="hidden" name="id" value="{{$user_profile['id']}}" />
					<div class="col-lg-6">
						<div class="form-group">
							<label>Nama Lengkap</label>
							<input type="text" class="form-control" name="name" value="{{$user_profile['name']}}">
						</div>
						<div class="form-group">
							<label>Alamat Email</label>
							<input type="text" class="form-control" name="email" value="{{$user_profile['email']}}">
						</div>
						<div class="form-group">
							<label>Password</label>
							<input type="password" class="form-control" name="password" value="" placeholder="Isi untuk mengganti password">
						</div>
						<div class="form-group">
							<label>Konfirmasi Password</label>
							<input type="password" class="form-control" name="cpassword" value="" placeholder="Isi untuk mengganti password">
						</div>
						<button type="submit" class="btn btn-primary">Submit Button</button>
						<button type="reset" class="btn btn-warning">Reset Button</button>
					</div>
					
					<div class="col-lg-12">
						<div class="form-group">
							<label>Nama Lengkap</label>
							<textarea class="form-control" name="name"></textarea>
						</div>
					</div>
				</form>



<div class="col-lg-12">
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-striped table-bordered table-hover" id="dataTables-example">
								<thead>
									<tr>
										<th>Rendering engine</th>
										<th>Browser</th>
										<th>Platform(s)</th>
										<th>Engine version</th>
										<th>CSS grade</th>
									</tr>
								</thead>
								
								<tbody>
									<tr class="odd gradeX">
										<td>Trident</td>
										<td>Internet Explorer 4.0</td>
										<td>Win 95+</td>
										<td class="center">4</td>
										<td class="center">X</td>
									</tr>
									<tr class="even gradeC">
										<td>Trident</td>
										<td>Internet Explorer 5.0</td>
										<td>Win 95+</td>
										<td class="center">5</td>
										<td class="center">C</td>
									</tr>
									<tr class="odd gradeA">
										<td>Trident</td>
										<td>Internet Explorer 5.5</td>
										<td>Win 95+</td>
										<td class="center">5.5</td>
										<td class="center">A</td>
									</tr>
									<tr class="even gradeA">
										<td>Trident</td>
										<td>Internet Explorer 6</td>
										<td>Win 98+</td>
										<td class="center">6</td>
										<td class="center">A</td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<div class="row">
							<div class="col-sm-6">
								<div class="dataTables_info" id="dataTables-example_info" role="alert" aria-live="polite" aria-relevant="all">
									Showing 1 to 10 of 57 entries
								</div>
							</div>
							
							<div class="col-sm-6">
								<div class="dataTables_paginate paging_simple_numbers" style="text-align:right">
									<ul class="pagination">
										<li class="paginate_button previous disabled" aria-controls="dataTables-example" tabindex="0" id="dataTables-example_previous"><a href="#">Previous</a></li>
										<li class="paginate_button active" aria-controls="dataTables-example" tabindex="0"><a href="#">1</a></li>
										<li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">2</a></li>
										<li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">3</a></li>
										<li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">4</a></li>
										<li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">5</a></li>
										<li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">6</a></li>
										<li class="paginate_button next" aria-controls="dataTables-example" tabindex="0" id="dataTables-example_next"><a href="#">Next</a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			
