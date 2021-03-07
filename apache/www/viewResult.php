<?php include("/var/www/html/private/print_head.php") ?>

<div id="viewResult">
	<h3>提交編號 {{sid}}：{{["Pending", "Compiling", "AC", "PE", "TLE", "MLE", "WA", "RE", "OLE", "CE", "SE", "NA"][status]}}{{time!="-1ms"?("(" + time + ", " + memory + ")"):""}}</h3><br/>
	<a v-bind:href="url">回題目</a>

	<div v-for="subproblem in testcases" style="margin-top:2em;">
		<h5> 子題 {{subproblem.index}}： </h5>
		<table class="table" id="subproblem" style="margin-top:1em;">
			<thead>
				<tr>
					<th>結果</th>
					<th>輸入</th>
					<th>輸出</th>
					<th>耗時</th>
					<th>記憶體</th>
					<th>錯誤訊息</th>
				</tr>
			</thead>
			<tbody v-for="tc in subproblem.testcases">
				<tr>
					<td>{{["Pending", "Compiling", "AC", "PE", "TLE", "MLE", "WA", "RE", "OLE", "CE", "SE", "NA"][tc.status]}}</td>
					<td style="white-space: pre-line;">{{tc.public?tc.input:"不公開"}}</td>
					<td style="white-space: pre-line;">{{tc.public?tc.output:"不公開"}}</td>
					<td>{{tc.time}}ms</td>
					<td>{{tc.memory}}kb</td>
					<td>{{tc.public?tc.description:"不公開"}}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div style="margin-top:1em;">
		<h5>程式碼：{{language}}</h5>
		<textarea id="code" name="code" rows="10" class="form-control" style="margin: 0px -4px 0px 0px; width: 100%;">{{code}}</textarea>
		<hr/>
		<h5 v-if="description">編譯器訊息：</h5>
		<div>{{description}}</div>
	</div>
</div>

<script>
	var app = new Vue({
		el: "#viewResult",
		data: {
			sid: -1,
			status: 0,
			time: -1,
			memory: -1,
			testcases: [],
			url: '',
			code: '',
			language: '',
			description: ''
		},
		beforeCreate(){
			let uri = window.location.search.substring(1); 
			let params = new URLSearchParams(uri);
			if(!params.has("id")){
				location.href = '/status';
				return;
			}
			axios.get("/api/queryVerdict.php", {params: {sid: params.get("id")}})
			.then(response=>{
                /* console.log(response.data); */
				var result = response.data.data;
                if(result == 0){
                    this.code = result.code;
                    return;
                }
				this.sid = params.get("id");
				this.status = result.status;
				if(result.time >= 1000){
					this.time = (result.time / 1000) + "s";
				} else {
					this.time = result.time + "ms";
				}
				if(result.memory >= 1024){
					this.memory = result.memory / 1024 + "mb";
				} else {
					this.memory = result.memory + "kb";
				}
				this.testcases = result.subproblems;
				this.url = "/showProblem?id=" + result.pid;
				this.code = result.code;
				this.language = result.language;
				this.description = result.description;
			})
			.catch(error=>{
				console.log(error)
			})
		}
	})	
</script>

<?php include("/var/www/html/private/print_foot.php") ?>
