<?php include("/var/www/html/private/print_head.php") ?>

<style type="text/css">
	.blue{
		color: blue;
	}
	.red{
		color: red;
	}
	.green{
		color: green;
	}
	.orange{
        color: orange;
	}
</style>

<div id="status">
	<p>{{message}}</p>
	<table class="table">
		<thead>
			<tr>
				<th style="text-align:center">提交編號</th>
				<th>提交者</th>
				<th>題目</th>
				<th>語言</th>
				<th>結果</th>
				<th>上傳時間</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="submission in list">
				<td style="text-align:center">{{submission.ID}}</td>
				<td>{{submission.Name}}</td>
				<td><a v-bind:href="submission.problemUrl">{{submission.Title}}</a></td>
				<td>{{submission.Language}}</td>
				<td><a v-bind:href="submission.url" v-bind:class="submission.color">{{submission.Status}}</a></td>
				<td>{{submission.Upload_time}}</td>
			</tr>
		</tbody>
	</table>
	第 <input style="width: 3em" v-model.number="page" type="number"> 頁 | 
	<span v-if="!first">《<a v-on:click="prev" href="#">上一頁</a>》</span>
	<span v-if="!last">《<a v-on:click="next" href="#">下一頁</a>》</span>
</div>

<script>
	var update = function(app){
		axios.get('/api/status.php', {
			params: {
				limit: 40,
				offset: (app.page - 1) * 40,
				pid: app.pid,
				uid: app.uid
			}
		})
		.then(response=>{
			app.list = [];
			if(response.data.data.num < 40) app.last = true;
			else app.last = false;
			response.data.data.submissions.forEach(item=>{
				item.url = "/viewResult?id=" + item.ID;
				if(item.Status == 2) item.color = "green";
				else if(item.Status == 1) item.color = "blue";
				else if(item.Status == 0) item.color = "orange";
				else item.color = "red";
				item.Status = ["Pending", "Compiling", "AC", "PE", "TLE", "MLE", "WA", "RE", "OLE", "CE", "SE", "NA"][item.Status];
				if(item.Name==log.Name||item.Auth) item.problemUrl = "/showProblem?id=" + item.ProblemID;
				app.list.push(item);
			});
		})
		.catch(error=>{
			app.message = error.response.message;
			console.log(error)
		});
	}
	var Status = new Vue({
		el: "#status",
		data: {
			message: "",
			list: [],
			page: 1,
			last: false,
			first: true,
			uid: -1,
			pid: -1
		},
		created(){
			let uri = window.location.search.substring(1);
			let params = new URLSearchParams(uri);
			if(params.has("pid")){
				this.pid = params.get("pid");
			}
			if(params.has("uid")){
				this.uid = params.get("uid");
			}
		},
		beforeMount(){
			update(this);
		},
		watch: {
			page: function(){
				if(this.page === "") return 0;
				if(this.page < 1) this.page = 1;
				update(this);
				this.first = this.page == 1;
			}
		},
		methods: {
			next: function(){
				this.page ++;
			},
			prev: function(){
				this.page --;
			}
		}
	});
</script>
<?php include("/var/www/html/private/print_foot.php") ?>
