<template>
	<div class="container">
		<div class="row mt-5">
			<div class="col-md-12">
				<div class="card" v-if="$gate.isAdmin()">
					<div class="card-header">
						<h3 class="card-title">Users Management</h3>

						<div class="card-tools">
							<button class="btn btn-success" @click="newModal">
								Add New
								<i class="fas fa-user-plus fa fw"></i>
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
									<th>Type</th>
									<th>Registered At</th>
									<th>Modify</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="user in users" :key="user.id">
									<td>{{ user.id }}</td>
									<td>{{ user.name }}</td>
									<td>{{ user.email }}</td>
									<td>{{ user.type | upText }}</td>
									<td>{{ user.created_at | myDate }}</td>

									<td>
										<a href="#" @click="editModal(user)">
											<i class="fas fa-edit blue"></i>
										</a>
										/
										<a href="#" @click="deleteUser(user.id)">
											<i class="fas fa-trash red"></i>
										</a>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<!-- /.card-body -->
				</div>
				<!-- /.card -->
			</div>
		</div>
		<div v-if="!$gate.isAdmin()">
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
							Update User's Info
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
					<form @submit.prevent="editmode ? updateUser() : createUser()">
						<div class="modal-body">
							<div class="form-group">
								<input
									v-model="form.name"
									type="text"
									name="name"
									placeholder="Name"
									class="form-control"
									:class="{ 'is-invalid': form.errors.has('name') }"
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
									:class="{ 'is-invalid': form.errors.has('email') }"
								/>
								<has-error :form="form" field="email"></has-error>
							</div>
							<div class="form-group">
								<textarea
									v-model="form.bio"
									id="bio"
									name="bio"
									placeholder="Shor bio for user (Optional)"
									class="form-control"
									:class="{ 'is-invalid': form.errors.has('bio') }"
								></textarea>
								<has-error :form="form" field="bio"></has-error>
							</div>
							<div class="form-group">
								<select
									v-model="form.type"
									id="type"
									class="form-control"
									:class="{ 'is-invalid': form.errors.has('type') }"
								>
									<option value>Select User Role</option>
									<option value="admin">Admin</option>
									<option value="user">User</option>
									<option value="author">Author</option>
								</select>
								<has-error :form="form" field="type"></has-error>
							</div>
							<div class="form-group">
								<input
									v-model="form.password"
									type="password"
									name="password"
									id="password"
									class="form-control"
									:class="{ 'is-invalid': form.errors.has('password') }"
								/>
								<has-error :form="form" field="password"></has-error>
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
				users: {},
				// Create a new form instance
				form: new Form({
					id: "",
					name: "",
					email: "",
					password: "",
					type: "",
					bio: "",
					photo: ""
				})
			};
		},

		methods: {
			editModal(user) {
				this.editmode = true;
				this.form.reset();
				$("#addNew").modal("show");
				this.form.fill(user);
			},
			newModal() {
				this.editmode = false;
				this.form.reset();
				$("#addNew").modal("show");
			},

			loadUsers() {
				if (this.$gate.isAdmin) {
					axios.get("api/user").then(({ data }) => (this.users = data.data));
				}
			},
			createUser() {
				this.$Progress.start();
				this.form
					.post("api/user")
					.then(() => {
						Fire.$emit("UpdateTable");
						$("#addNew").modal("hide");
						toast.fire({
							icon: "success",
							title: "User Created Successfully"
						});
						this.$Progress.finish();
					})
					.catch(() => {});
			},

			updateUser() {
				this.$Progress.start();
				this.form
					.put("api/user/" + this.form.id)
					.then(() => {
						$("#addNew").modal("hide");
						toast.fire({
							icon: "success",
							title: "User Info's Updated Successfully"
						});
						Fire.$emit("UpdateTable");
						this.$Progress.finish();
					})
					.catch(() => {
						this.$Progress.fail();
					});
			},

			deleteUser(id) {
				swal
					.fire({
						title: "Are you sure?",
						text: "You won't be able to revert this!",
						icon: "warning",
						showCancelButton: true,
						confirmButtonColor: "#3085d6",
						cancelButtonColor: "#d33",
						confirmButtonText: "Yes, delete it!"
					})
					.then(result => {
						// Send request to the servers
						if (result.value) {
							this.form
								.delete("api/user/" + id)
								.then(() => {
									swal.fire("Deleted!", "User Deleted", "success");
									Fire.$emit("UpdateTable");
								})
								.catch(() => {
									swal.fire("Failed!", "There was something wrong", "warning");
								});
						}
					});
			}
		},

		created() {
			this.loadUsers();
			Fire.$on("UpdateTable", () => {
				this.loadUsers();
			});
			//setInterval(() => this.loadUsers(), 3000);
		}
	};
</script>
