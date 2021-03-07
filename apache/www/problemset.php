<?php include("/var/www/html/private/print_head.php") ?>


<div id="problemset">
	<p>{{message}}</p>
	<table class="table">
		<thead>
			<tr>
				<th style="text-align:center">題號</th>
				<th>題目名稱</th>
				<th>標籤</th>
				<th>題目來源</th>
				<th>通過率</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="problem in list">
				<td style="text-align:center">{{problem["ID"]}}</td>
				<td><a v-bind:href="problem.url">{{problem["Title"]}}</a></td>
				<td>{{problem["tags"]}}</td>
				<td>{{problem["Source"]}}</td>
				<td>{{problem["AC_times"]}}/{{problem["Submit_Times"]}}</td>
			</tr>
		</tbody>
	</table>
	第 <input style="width: 3em" v-model.number="page" type="number"> 頁 | 
	<span v-if="!first">《<a v-on:click="prev" href="#">上一頁</a>》</span>
	<span v-if="!last">《<a v-on:click="next" href="#">下一頁</a>》</span>
</div>

<script>
	async function update(app) {
		async function mapItem(item) {
			let tags = ["入門", "簡單", "中等", "困難"][item.Difficulty]
			/* const res = await axios.get('/api/queryTag.php', { params: { pid: item.ID }}) */
			item.tags.forEach(tag => { tags += `, ${tag}` })
			item.tags = tags
			item.url = `/showProblem?id=${item.ID}`
			return item
		}

		try {
			const response = await axios.get('/api/list.php', {
				params: {
					limit: 20,
					offset: (app.page - 1) * 20
				}
			})
			app.list = []
			app.last = response.data.data.num < 20
			arr = [] // What is this stupid array?

			const awaitedResults = response.data.data.problems.map(mapItem)
			const results = await Promise.all(awaitedResults)
			results.sort((a, b) => parseInt(b.ID) - parseInt(a.ID))
			app.list = results
		} catch (error) {
            console.log(error)
			/* app.message = error.response.message; */
		}
	}
/*
	var update = function(app){
		axios.get('/api/list.php', {
			params: {
				limit: 20,
				offset: (app.page - 1) * 20
			}
		})
		.then(response=>{
			app.list = [];
			if(response.data.data.num < 20) app.last = true;
			else app.last = false;

			arr = []

			response.data.data.problems.forEach(item=>{
				var tags = ["入門","簡單","中等","困難"][item.Difficulty];
				axios.get('/api/queryTag.php', {params: {pid: item.ID}})
				.then(function(res){
					res.data.data.forEach(tag=>{
						tags = tags + ", " + tag;
					})
					item.tags = tags;
					item.url = "/showProblem?id=" + item.ID;
					app.list.push(item);
				})
				.catch(error=>{
					app.message = error.response.message;
				});
			});
		})
		.catch(error=>{
			app.message = error.response.message;
			// console.log(error)
		});
	}
*/
	var plist = new Vue({
		el: "#problemset",
		data: {
			message: "",
			list: [],
			page: 1,
			last: false,
			first: true
		},
		beforeMount() {
			update(this);
		},
		watch: {
			async page() {
				if (this.page === "") return 0;
				if (this.page < 1) this.page = 1;
				await update(this);
				this.first = this.page == 1;
			}
		},
		methods: {
			next() {
				this.page ++;
			},
			prev() {
				this.page --;
			}
		}
	});
</script>

<?php include("/var/www/html/private/print_foot.php") ?>
