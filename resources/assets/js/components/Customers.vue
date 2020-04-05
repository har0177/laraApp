<template>
	<div class="container">
		<div class="row mt-5">
			<div class="col-md-12">
				<div class="card" v-if="$gate.isAdminOrAuthor()">
					<div class="card-header">
						<h3 class="card-title">Customers Management</h3>

						<div class="card-tools">
							<button class="btn btn-success" @click="newModal">
								Add New
								<i class="fas fa-user-friends fa fw"></i>
							</button>
						</div>
					</div>
					<!-- /.card-header -->
					<div class="card-body table-responsive p-0">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>ID</th>
									<th>Name</th>
									<th>Email</th>
									<th>Phone</th>
									<th>Address</th>
									<th>Modify</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="customer in customers.data" :key="customer.id">
									<td>{{ customer.id }}</td>
									<td>{{ customer.name | upText }}</td>
									<td>{{ customer.email }}</td>
									<td>{{ customer.phone }}</td>
									<td>{{ customer.address }}</td>

									<td>
										<a href="#" @click="editModal(customer)">
											<i class="fas fa-edit blue"></i>
										</a>
										/
										<a href="#" @click="deletecustomer(customer.id)">
											<i class="fas fa-trash red"></i>
										</a>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<!-- /.card-body -->
					<div class="card-footer">
						<pagination
							:data="customers"
							@pagination-change-page="getResults"
						></pagination>
					</div>
				</div>
				<!-- /.card -->
			</div>
		</div>
		<div v-if="!$gate.isAdminOrAuthor()">
			<not-found></not-found>
		</div>

		<div
			class="modal fade"
			id="addNew"
			tabindex="-1"
			role="dialog"
			aria-labelledby="addNewLabel"
			aria-hidden="true"
		>
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" v-show="!editmode" id="addNewLabel">
							Add Mew
						</h5>
						<h5 class="modal-title" v-show="editmode" id="addNewLabel">
							Update customer's Info
						</h5>
						<button
							type="button"
							class="close"
							data-dismiss="modal"
							aria-label="Close"
						>
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<form
						@submit.prevent="editmode ? updatecustomer() : createcustomer()"
					>
						<div class="modal-body">
							<div class="form-group">
								<input
									v-model="form.name"
									type="text"
									name="name"
									placeholder="Name"
									class="form-control"
									:class="{'is-invalid': form.errors.has('name')}"
								/>
								<has-error :form="form" field="name"></has-error>
							</div>
							<div class="form-group">
								<input
									v-model="form.email"
									type="email"
									name="email"
									placeholder="Email Address"
									class="form-control"
									:class="{'is-invalid': form.errors.has('email')}"
								/>
								<has-error :form="form" field="email"></has-error>
							</div>
							<div class="form-group">
								<textarea
									v-model="form.address"
									id="address"
									name="address"
									placeholder="Address of customer (Optional)"
									class="form-control"
									:class="{'is-invalid': form.errors.has('address')}"
								></textarea>
								<has-error :form="form" field="address"></has-error>
							</div>

							<div class="form-group">
								<input
									v-model="form.phone"
									type="phone"
									name="phone"
									id="phone"
									placeholder="Phone Number"
									class="form-control"
									:class="{'is-invalid': form.errors.has('phone')}"
								/>
								<has-error :form="form" field="phone"></has-error>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-danger" data-dismiss="modal">
								Close
							</button>
							<button type="submit" v-show="!editmode" class="btn btn-success">
								Create
							</button>
							<button type="submit" v-show="editmode" class="btn btn-success">
								Update
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				editmode: false,
				customers: {},
				// Create a new form instance
				form: new Form({
					id: "",
					name: "",
					email: "",
					phone: "",
					address: "",
				}),
			};
		},

		methods: {
			getResults(page = 1) {
				axios.get("api/customer?page=" + page).then((response) => {
					this.customers = response.data;
				});
			},

			editModal(customer) {
				this.editmode = true;
				this.form.reset();
				$("#addNew").modal("show");
				this.form.fill(customer);
			},
			newModal() {
				this.editmode = false;
				this.form.reset();
				$("#addNew").modal("show");
			},

			loadcustomers() {
				if (this.$gate.isAdminOrAuthor()) {
					axios.get("api/customer").then(({data}) => (this.customers = data));
				}
			},
			createcustomer() {
				this.$Progress.start();
				this.form
					.post("api/customer")
					.then(() => {
						Fire.$emit("UpdateTable");
						$("#addNew").modal("hide");
						toast.fire({
							icon: "success",
							title: "Customer Created Successfully",
						});
						this.$Progress.finish();
					})
					.catch(() => {});
			},

			updatecustomer() {
				this.$Progress.start();
				this.form
					.put("api/customer/" + this.form.id)
					.then(() => {
						$("#addNew").modal("hide");
						toast.fire({
							icon: "success",
							title: "Customer Info's Updated Successfully",
						});
						Fire.$emit("UpdateTable");
						this.$Progress.finish();
					})
					.catch(() => {
						this.$Progress.fail();
					});
			},

			deletecustomer(id) {
				swal
					.fire({
						title: "Are you sure?",
						text: "You won't be able to revert this!",
						icon: "warning",
						showCancelButton: true,
						confirmButtonColor: "#3085d6",
						cancelButtonColor: "#d33",
						confirmButtonText: "Yes, delete it!",
					})
					.then((result) => {
						// Send request to the servers
						if (result.value) {
							this.form
								.delete("api/customer/" + id)
								.then(() => {
									swal.fire("Deleted!", "Customer Deleted", "success");
									Fire.$emit("UpdateTable");
								})
								.catch(() => {
									swal.fire("Failed!", "There was something wrong", "warning");
								});
						}
					});
			},
		},

		created() {
			Fire.$on("searching", () => {
				let query = this.$parent.search;
				axios
					.get("api/findCustomer?q=" + query)
					.then((data) => {
						this.customers = data.data;
					})
					.catch(() => {});
			});
			this.loadcustomers();
			Fire.$on("UpdateTable", () => {
				this.loadcustomers();
			});
			//setInterval(() => this.loadcustomers(), 3000);
		},
	};
</script>
