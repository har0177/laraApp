<template>
	<div class="container">
		<div class="row mt-5">
			<div class="col-md-12">
				<div class="card" v-if="$gate.isAdminOrAuthor()">
					<div class="card-header">
						<h3 class="card-title">Category Management</h3>

						<div class="card-tools">
							<button class="btn btn-success" @click="newModal">
								Add New
								<i class="fas fa-layer-group fa fw"></i>
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
								
									<th>Modify</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="cat in category.data" :key="cat.id">
									<td>{{ cat.id }}</td>
									<td>{{ cat.name | upText }}</td>
									

									<td>
										<a href="#" @click="editModal(cat)">
											<i class="fas fa-edit blue"></i>
										</a>
										/
										<a href="#" @click="deletecat(cat.id)">
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
							:data="category"
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
							Update Category Info
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
					<form @submit.prevent="editmode ? updatecat() : createcat()">
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
				category: {},
				// Create a new form instance
				form: new Form({
					id: "",
					name: ""
				}),
			};
		},

		methods: {
			getResults(page = 1) {
				axios.get("api/category?page=" + page).then((response) => {
					this.category = response.data;
				});
			},

			editModal(cat) {
				this.editmode = true;
				this.form.reset();
				$("#addNew").modal("show");
				this.form.fill(cat);
			},
			newModal() {
				this.editmode = false;
				this.form.reset();
				$("#addNew").modal("show");
			},

			loadCategory() {
				if (this.$gate.isAdminOrAuthor()) {
					axios.get("api/category").then(({data}) => (this.category = data));
				}
			},
			createcat() {
				this.$Progress.start();
				this.form
					.post("api/category")
					.then(() => {
						Fire.$emit("UpdateTable");
						$("#addNew").modal("hide");
						toast.fire({
							icon: "success",
							title: "Category Created Successfully",
						});
						this.$Progress.finish();
					})
					.catch(() => {});
			},

			updatecat() {
				this.$Progress.start();
				this.form
					.put("api/category/" + this.form.id)
					.then(() => {
						$("#addNew").modal("hide");
						toast.fire({
							icon: "success",
							title: "Category Info's Updated Successfully",
						});
						Fire.$emit("UpdateTable");
						this.$Progress.finish();
					})
					.catch(() => {
						this.$Progress.fail();
					});
			},

			deletecat(id) {
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
								.delete("api/category/" + id)
								.then(() => {
									swal.fire("Deleted!", "Category Deleted", "success");
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
					.get("api/findCategory?q=" + query)
					.then((data) => {
						this.category = data.data;
					})
					.catch(() => {});
			});
			this.loadCategory();
			Fire.$on("UpdateTable", () => {
				this.loadCategory();
			});
			//setInterval(() => this.loadCategory(), 3000);
		},
	};
</script>
