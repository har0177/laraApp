
<template>
	<div class="container">
		<div class="row mt-5">
			<div class="col-md-12">
				<div class="card" v-if="$gate.isAdminOrAuthor()">
					<div class="card-header">
						<h3 class="card-title">Products Management</h3>

						<div class="card-tools">
							<button class="btn btn-success" @click="newModal">
								Add New
								<i class="fab fa-product-hunt fa fw"></i>
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
									<th>Category</th>
									<th>Pprice</th>
									<th>Sprice</th>
									<th>Wprice</th>
									<th>Modify</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="product in products.data" :key="product.id">
									<td>{{ product.id }}</td>
									<td>{{ product.name | upText }}</td>
									<td>{{ product.cat_name | upText }}</td>
									<td>{{ product.pprice }}</td>
									<td>{{ product.sprice }}</td>
									<td>{{ product.wprice }}</td>

									<td>
										<a href="#" @click="editModal(product)">
											<i class="fas fa-edit blue"></i>
										</a>
										/
										<a href="#" @click="deleteproduct(product.id)">
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
							:data="products"
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
							Update product's Info
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
					<form @submit.prevent="editmode ? updateproduct() : createproduct()">
						<div class="modal-body">
							<div class="form-group">
								<select2 :options="categories" v-model="form.cat_id" :class="{'is-invalid': form.errors.has('cat_id')}">
                                 </select2>
								 <has-error :form="form" field="cat_id"></has-error>
							</div>
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
									v-model="form.pprice"
									type="number"
									name="pprice"
									placeholder="Purchase Price"
									class="form-control"
									:class="{'is-invalid': form.errors.has('pprice')}"
								/>
								<has-error :form="form" field="pprice"></has-error>
							</div>
							<div class="form-group">
								<input
									v-model="form.sprice"
									type="number"
									name="sprice"
									placeholder="Sale Price"
									class="form-control"
									:class="{'is-invalid': form.errors.has('sprice')}"
								/>
								<has-error :form="form" field="sprice"></has-error>
							</div>
							<div class="form-group">
								<input
									v-model="form.wprice"
									type="number"
									name="wprice"
									placeholder="Whole Sale Price"
									class="form-control"
									:class="{'is-invalid': form.errors.has('wprice')}"
								/>
								<has-error :form="form" field="wprice"></has-error>
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
				products: {},
				categories: [],
				// Create a new form instance
				form: new Form({
					id: "",
					name: "",
					cat_id: "",
					pprice: "",
					sprice: "",
					wprice: "",
				}),
			};
		},

		methods: {
			
			
			
			getResults(page = 1) {
				axios.get("api/product?page=" + page).then((response) => {
					this.products = response.data;
				});
			},

			editModal(product) {
				this.editmode = true;
				this.form.reset();
				$("#addNew").modal("show");
				this.form.fill(product);
			},
			newModal() {
				this.editmode = false;
				this.form.reset();
				$("#addNew").modal("show");
			},
			
			loadProducts() {
				if (this.$gate.isAdminOrAuthor()) {
					axios.get("api/product").then(({data}) => (this.products = data));
				}
			},
			loadCategories() {
				axios.get("api/categories").then((response) => {
					this.categories = response.data;
				});
			},
			createproduct() {
				this.$Progress.start();
				this.form
					.post("api/product")
					.then(() => {
						Fire.$emit("UpdateTable");
						$("#addNew").modal("hide");
						toast.fire({
							icon: "success",
							title: "Product Created Successfully",
						});
						this.$Progress.finish();
					})
					.catch(() => {});
			},

			updateproduct() {
				this.$Progress.start();
				this.form
					.put("api/product/" + this.form.id)
					.then(() => {
						$("#addNew").modal("hide");
						toast.fire({
							icon: "success",
							title: "Product Info's Updated Successfully",
						});
						Fire.$emit("UpdateTable");
						this.$Progress.finish();
					})
					.catch(() => {
						this.$Progress.fail();
					});
			},

			deleteproduct(id) {
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
								.delete("api/product/" + id)
								.then(() => {
									swal.fire("Deleted!", "Product Deleted", "success");
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
					.get("api/findProduct?q=" + query)
					.then((data) => {
						this.products = data.data;
					})
					.catch(() => {});
			});
			this.loadProducts();
			this.loadCategories();
			Fire.$on("UpdateTable", () => {
				this.loadProducts();
			});
			//setInterval(() => this.loadProducts(), 3000);
		},
	};
	
</script>
