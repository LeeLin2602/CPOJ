<?php include("/var/www/html/private/print_head.php") ?>

<style type="text/css">
	.problembox {
		white-space: pre-line;
	}
	.mathjax {
		max-width: 100%;
	}
</style>
<div id="reader">
	<div class="row" style="margin:2em;">
		<div class="col-md-2">
			<a href="/listProblems">回題庫</a> 	
		</div>
		<div class="col-md-6 text-center">
			<div class="h1">
				<br>{{ID}}. {{Title}}<br><br>
			</div>
		</div>
		<div class="col-md-4">
			標籤：{{Tags}}<br>
			來源：{{Source}}<br>
			通過率：{{AC_times}}/{{Submit_times}}<br>
			難度：{{["入門","簡單","中等","困難"][Difficulty]}}<br>
		</div>
	</div>

	<div class="row" style="">
		<div class="col-xs-1 " style="width:100%">

			<div class="card h-100"  style="width:100%; margin-bottom: 5em;">
				<h4 class="card-header" style="text-align: center;">題目敘述</h4>
				<div class="card-body">
					<div id="Problem" class="container" style="margin-bottom: 4em;"></div>
					<hr/>
					<div class="container">
						<div class="row">
							<div class="col-md-6">
								<div class="panel panel-default">
									<div class="panel-heading">
										輸入說明：
									</div>
									<div class="panel-body">
										<br/>
										<div id="input_format" class="problembox"></div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="panel panel-default">
									<div class="panel-heading">
										輸出說明：
									</div>
									<div class="panel-body">
										<br/>
										<div id="output_format" class="problembox"></div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="panel panel-default">
									<div class="panel-heading">
										測資限制：
									</div>
									<div class="panel-body">
										<br/>
										<div id="testcaseInfo" class="problembox"></div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="container" style="margin-top: 2em">
						<div class="row" v-for="example in IOExamples">
							<div class="col-md-6">
								<div class="panel panel-default">
									<div class="panel-heading">
										範例輸入：
									</div>
									<div class="panel-body">
										<br/>
										<div class="problembox">
											{{example[0]}}
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="panel panel-default">
									<div class="panel-heading">
										範例輸出：
									</div>
									<div class="panel-body">
										<br/>
										<div class="problembox">
											{{example[1]}}
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row" style="margin-top: 1em">		
			<div class="col">		
				<button class="btn btn-primary" v-on:click="()=>{showSubmitForm=!showSubmitForm;}">提交程式碼</button>
				<a v-bind:href="status" class="btn btn-primary" style="margin-left: 1em; margin-right: 1em;">本題狀態</a>
				<a href="" class="btn btn-primary">本題討論</a><br><br>
			</div>
		</div>
		<div class="container" id="submitForm" v-if="showSubmitForm">
			<div class="row">
				<textarea id="code" name="code" v-model="code" rows="10" class="form-control" style="margin: 0px -4px 0px 0px; width: 100%;"></textarea>
			</div>
			<div id="Compilers" class="row" style="text-align: left; margin-top: 1em">
				<div style="display: block; clear: both;">
					解題語言：<br>
					
					<input v-model="language" name="language" id="C" type="radio" value="C">
					<span style="font-weight: bold; font-size: large">C</span>: gcc -std=c11(gcc 7.4.0)<br>
			
					<input v-model="language" name="language" id="CPP" type="radio" value="CPP" checked="true">
					<span style="font-weight: bold; font-size: large">CPP</span>: g++ -std=c++14(g++ 7.4.0)<br>

					<input v-model="language" name="language" id="JAVA" type="radio" value="JAVA">
					<span style="font-weight: bold; font-size: large">JAVA</span>: OpenJDK java version 11.0.7 （class名稱必須為"P13"）<br>

					<input v-model="language" name="language" id="PYTHON2" type="radio" value="PYTHON2">
					<span style="font-weight: bold; font-size: large">PYTHON2</span>: Python 2.7.17<br>

					<input v-model="language" name="language" id="PYTHON3" type="radio" value="PYTHON3">
					<span style="font-weight: bold; font-size: large">PYTHON3</span>: Python 3.7.5<br>
				</div>
			</div>
			<div class="row justify-content-center align-items-center">
				<button class="btn btn-primary" v-on:click="submit">提交</button>
			</div>
		</div>
	</div>
</div>

<script>

var problem = new Vue({
	el: "#reader",
	data: {
		ID: -1,
		Tags: '',
		Title: '',
		Source: '',
		AC_times: 0,
		Submit_times: 0,
		Difficulty: 0,
		Content: 0,
		InputExplanation: '',
		OutputExplanation: '',
		TestCasesInfo: '',
		IOExamples: [],
		message: '',
		submitUrl: '',
		status: '',
		language: 'CPP',
		code: '',
		showSubmitForm: false
	},
	beforeCreate(){
		let uri = window.location.search.substring(1); 
		let params = new URLSearchParams(uri);
		if(!params.has("id")){
			location.href = '/listProblems';
			return;
		}
		axios.get('/api/getProblem.php', {params: {pid: params.get("id")}})
		.then(response=>{
			let problem = response.data.data;

			axios.get('/api/queryTag.php', {params: {pid: problem.ID}})
			.then(res=>{
				var tags = "";

				res.data.data.forEach(tag=>{
					tags = tags + ", " + tag;
				})
				
				this.ID = problem.ID;
				this.Title = problem.Title;
				this.Source = problem.Source;
				this.AC_times = problem.AC_times;
				this.Submit_times = problem.Submit_Times;
				this.Difficulty = problem.Difficulty;
				this.Content = problem.Problem.Content;
				this.InputExplanation = problem.Problem.InputExplanation;
				this.OutputExplanation = problem.Problem.OutputExplanation;
				this.TestCasesInfo = problem.Problem.TestCasesInfo;
				this.IOExamples = problem.Problem.IOExample;
				this.submitUrl = "/submitSolution?id=" + problem.ID;
				this.status = "/status?pid=" + problem.ID;
				this.Tags = tags.substr(2);

				const promise = new Promise(function(){
					markjax(problem.Problem.Content, document.getElementById('Problem'));
					markjax(problem.Problem.InputExplanation, document.getElementById('input_format'));
					markjax(problem.Problem.OutputExplanation, document.getElementById('output_format'));
					markjax(problem.Problem.TestCasesInfo, document.getElementById('testcaseInfo'));
				});

				// console.log(this.Tags)
			})
			.catch(error=>{
				// console.log(error)
				this.message = error.response.message;
			});

		})
		.catch(error=>{
			this.message = error.response.data.message;
		})
	},
	methods: {
		submit: function(){
			console.log(this.ID)
			axios.post("/api/submit.php", {
				pid: this.ID,
				code: this.code,
				language: this.language
			})
			.then(response=>{
				location.href = "/status.php";
			})
			.catch(error=>{
				console.log(error.response)
				if(error.response.status == 401) alert("登入失效");
				else if(error.response.status == 400) alert("請重新刷新頁面");
				else alert(error.response.data.message);
			})
		}
	}
})
</script>

<?php include("/var/www/html/private/print_foot.php") ?>