#!/usr/bin/python3
from multiprocessing import Pool, Manager
import pymysql
import time, os, subprocess
import json

def compile_code(file_name, language):
    compilers = {
        "C"    : "gcc %s -o %s_ -Wall -lm -O2 -std=c11",
        "CPP"    : "g++ %s -o %s_ -O2 -Wall -lm -std=c++14",
        "JAVA"   : "javac %s",
        "PYTHON2": '''python2.7 -c "import py_compile; py_compile.compile('%s', '%s_')"''',
        "PYTHON3": '''python3.7 -c "import py_compile; py_compile.compile('%s', '%s_')"'''
        }
    if language != "JAVA":
        cmd = compilers[language] % (file_name, file_name)
    else:
        cmd = compilers[language] % (file_name)
    p = subprocess.Popen(cmd, shell=True, cwd="/tmp", stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    out,err =  p.communicate()
    
    if p.returncode == 0:
        return (True, "")
    
    return (False,err.decode())


processingNum = 4
path = [""] * processingNum

for i in range(processingNum):
    out_r, out_w = os.pipe()
    out_r, out_w = os.fdopen(out_r, "r"), os.fdopen(out_w, "w")

    subprocess.run(["isolate", "--cg" , "--box-id=" + str(i), "--cleanup"])
    subprocess.run(["isolate", "--cg" ,"--box-id=" + str(i), "--init"], stdout = out_w)
    path[i] = out_r.readline().replace("\n", "") + "/box/"
    out_w.close()


def get_from_mysql(q, que):
    while True:
        try:
            db = pymysql.connect(host="172.19.0.3", user="judge", password="a84dc85b974", database="oj")
            break
        except Exception as e:
            # if e == pymysql.err.OperationalError:
            print(e)
            time.sleep(2)

    while True:
        try:
            cur = db.cursor()
            num = cur.execute("SELECT ID, Language, ProblemID, Submitter FROM `Solutions` WHERE `Status` = 0")

            for i in cur.fetchall():
                cur.execute(f"UPDATE `Solutions` SET `Status`=1 WHERE `ID`={i[0]}")
                q.put(i)

            db.commit()

            while que.qsize() > 0:
                try:
                    a, b, c = que.get()
                    
                    cur.execute(f"UPDATE `Solutions` SET `Status`={a} WHERE `ID`={b[0]}")

                    if a == 2:
                        cur.execute(f"UPDATE `Problems` SET `AC_Times`= AC_Times + 1 WHERE `ID`={b[2]}")
                        cur.execute(f"UPDATE `Accounts` SET `AC_Times`= AC_Times + 1 WHERE `ID`={b[3]}")

                    cur.execute(f"INSERT INTO Static (ProblemID, Submitter, Status, Score) VALUES ({b[2]}, {b[3]}, {a}, {c}) ON DUPLICATE KEY UPDATE Static.Status = IF(Static.Status = 2, 2, {a}), Static.Score = IF(Score < {c}, {c}, Score)")

                except Exception as e:
                    que.put((a, b, c))
                    print(e)
            db.commit()

            time.sleep(2)
        except Exception as e:
            db.commit()
            print(e)


def worker(Sol, wid, path, queue, busy):
    run = {
        "C"    : "./",
        "CPP"    : "./",
        "JAVA"   : "/usr/lib/jvm/java-11-openjdk-amd64/bin/java",
        "PYTHON2": "/usr/bin/python",
        "PYTHON3": "/usr/bin/python3.7"
        }
    queue.put((1, Sol, 0))

    try:
        file_type = {'C': '.c', 'CPP':'.cpp','PYTHON2':'', 'PYTHON3':'', 'JAVA':'.java'}

        problem = json.loads(open("/var/www/problems/" + str(Sol[2]) + ".json", "r").read())

        file_name = "P" + str(Sol[2])
        subprocess.run(["cp", "/var/www/submissions/" + str(Sol[0]) , path + file_name + file_type[Sol[1]]])


        status = {'Subproblems': []}
        
        

        tmp1, tmp2 = compile_code(path + file_name + file_type[Sol[1]], Sol[1])
        
        if Sol[1] == "JAVA":
            file_name += ""
        else:
            file_name += file_type[Sol[1]] + "_"

        if not tmp1:
            status['status'] = 9
            status['Description'] = tmp2.replace(path, "/")
            result = open("/var/www/verdicts/" + str(Sol[0]) + ".json", "w")
            result.write(json.dumps(status))
            result.close()
            queue.put((9 ,Sol, 0))
            busy[wid] = False
            return
        system_err = True
    except Exception as e:
        status['status'] = 10
        status['Description'] = e.replace(path, "/")
        result = open("/var/www/verdicts/" + str(Sol[0]) + ".json", "w")
        result.write(json.dumps(status))
        result.close()
        queue.put((10, Sol, 0))
        busy[wid] = False
        return

    score = 0
    final_status = True

    time_cost = 0
    memory_cost = 0
    testcase_num = 0

    for j in problem["Subproblems"]:
        status['Subproblems'].append({"Testcases":[]})
        status['Subproblems'][-1]['Debugger'] = j['Debugger']
        result_status = True
        for k in j['Testcases']:
            try:
                input_file = open(path + "input", 'w')
                input_file.write(k[0])
                input_file.close()
            except Exception as e:
                status['Subproblems'][-1]["Testcases"].append({'status': 10, 'Time': 0, 'Memory': 0, 'Input':k[0], 'Answer':  k[1], 'Output': '', 'Description': str(e) + "Fatal error when importing input_file."})
                result_status = False
                continue;            
            try:
                if run[Sol[1]] == "./":
                    subprocess.run(["isolate","--box-id=" + str(wid),'--time=' + str(int(k[2]) / 1000), '--cg', '--cg-mem=' + str(int(k[3]) * 1024), '--mem=' + str(int(k[3]) * 1024), "--stdin=input", "--stderr=err" , "--stdout=output" , "--meta=" + path + "meta", "--run", run[Sol[1]] + file_name])
                    
                elif Sol[1] == "JAVA":
                    subprocess.run(["isolate","--box-id=" + str(wid),'--time=' + str(int(k[2]) / 1000), '--cg', '--cg-mem=' + str(int(k[3]) * 1536), '-p', "--stdin=input", "--stderr=err"  , "--stdout=output" , "--meta=" + path + "meta", "--run", run[Sol[1]] , file_name])
                    
                else:
                    subprocess.run(["isolate","--box-id=" + str(wid),'--time=' + str(int(k[2]) / 500), '--cg', '--cg-mem=' + str(int(k[3]) * 1024), '--mem=' + str(int(k[3]) * 1024), "--stderr=err", "--stdin=input", "--stdout=output" , "--meta=" + path + "meta", "--run", run[Sol[1]] ,   file_name])
                
                try:
                    output_file = open(path + "output", "r")
                    output = output_file.read()
                    output_file.close()
                    output = output.replace(path, "/")
                except Exception:
                    output = ""
                    
                try:
                    err_file = open(path + "err", "r")
                    err = err_file.read()
                    err = err.replace(path, "/")
                    err_file.close()
                except Exception:
                    err = ""


                meta_file = open(path + "meta", "r")
                meta = meta_file.read()
                meta_file.close()
                meta_file = {}
                for m in meta.split("\n"):
                    if ":" in m:
                        meta_file[m.split(":")[0]] = m.split(":")[1] 

                time = memory = 0
                status_code = 0

                if 'status' in meta_file or ('exitcode' in meta_file and meta_file['exitcode'] != "0"):
                    status_code = {"RE": 7, "TO": 4, "SG": 7, "XX": 10}[meta_file['status']]
                    
                    if 'exitsig' in meta_file and meta_file['exitsig'] == "11":
                        err = "錯誤編號 11：不允許的記憶體調用。"
                    elif 'exitsig' in meta_file:
                        err = "錯誤編號 " + meta_file['exitsig']

                if status_code == 0:
                    time = int(float(meta_file['time']) * 1000)
                    memory = round(int(meta_file['max-rss'])  / 1024, 1)
                    if output == k[1]:
                        status_code = 2
                        time_cost += time
                        memory_cost += memory
                        testcase_num += 1
                    elif output.strip() == k[1].strip():
                        status_code = 2
                        time_cost += time
                        memory_cost += memory
                        testcase_num += 1
                    elif (output.replace(" ", "").replace("\n", "").replace("    ", "").replace("\r", "") == k[1].replace(" ", "").replace("\n", "").replace("    ", "").replace("\r", "")):
                        status_code = 3
                        time_cost += time
                        memory_cost += memory
                        testcase_num += 1
                    elif len(output) >= 2 ** 32 and len(output) >= len(k[1]):
                        output = output[2 ** 16:]
                        status_code = 8
                    else:
                        status_code = 6
                if status_code != 2:
                    result_status = False
                status['Subproblems'][-1]["Testcases"].append({'status': status_code, 'Time': time, 'Memory': memory, 'Input':k[0], 'Answer':  k[1], 'Output': output, 'Description': err})
                
            except Exception as e:
                result_status = False
                status['Subproblems'][-1]["Testcases"].append({'status': 10, 'Time': 0, 'Memory': 0, 'Input':k[0], 'Answer':  k[1], 'Output': '', 'Description': str(e) + "Fatal error when running."})
        
        status['Subproblems'][-1]['Score'] = j['Score']

        if result_status:
            score += j['Score']
            status['Subproblems'][-1]['GetScore'] = j['Score']
        else:
            status['Subproblems'][-1]['GetScore'] = 0
            result_status = True
            final_status = False


    status['status'] = 2 if final_status else 11;
    status['score'] = score
    status['time'] = round(time_cost / testcase_num, 1) if testcase_num != 0 else 0
    status['memory'] = round(memory_cost / testcase_num, 1) if testcase_num != 0 else 0

    queue.put((status['status'], Sol, score))

    result = open("/var/www/verdicts/" + str(Sol[0]) + ".json", "w")
    result.write(json.dumps(status))
    result.close()
    subprocess.run(['rm', '-r', path])
    subprocess.run(['mkdir', path])
    busy[wid] = False
    

def main():
    with Pool(processingNum) as p:
        mng = Manager()
        q = mng.Queue()
        que = mng.Queue()
        busy = mng.list([False] * processingNum)
        daemon = p.apply_async(get_from_mysql, args=(q, que))
        while True:
            for i in range(processingNum):
                if q.qsize() > 0 and not busy[i]:
                    busy[i] = True
                    p.apply_async(worker, args=(q.get(), i, path[i], que, busy))
            time.sleep(0.2)
main()

