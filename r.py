import praw
import inspect
import cymysql
import datetime
import time

try:
	cnx = cymysql.connect(host='host', port=3306, user='usr', passwd='passwd', db='db')
except:
	print ("oops")
c = cnx.cursor()
reddit = praw.Reddit(client_id='id',
                     client_secret='secret',
                     user_agent='youragent')

subr = reddit.subreddit('all')
for submission in subr.new():
	lastSub = submission;
i=0;

sdate="24/02/2017/16/55/00"
startStamp= int(time.mktime(datetime.datetime.strptime(sdate, "%d/%m/%Y/%H/%M/%S").timetuple()))
step=20
endStamp=startStamp+step-1

while True:
	#for submission in subr.new(limit=100, params={"after": lastSub.fullname}):
	print ("From", datetime.datetime.fromtimestamp(startStamp).strftime('%Y-%m-%d %H:%M:%S'), "to", datetime.datetime.fromtimestamp(endStamp).strftime('%Y-%m-%d %H:%M:%S'))
	for submission in subr.search("timestamp:"+str(startStamp)+".."+str(endStamp), syntax='cloudsearch'):
		i+=1;
		comments = list(submission.comments)
		for comm in comments:
			if not type(comm) is praw.models.MoreComments:
				v = (None, comm.body, datetime.datetime.fromtimestamp(comm.created).strftime('%Y-%m-%d %H:%M:%S'))
				try:
					c.execute("insert into comments (commID,body,time) values (%s,%s,%s)", v)
				except:
					print ("Problem?")
		cnx.commit()
		print(i);
		#print ("Processed", submission.title, submission.created)
	endStamp=startStamp-1
	startStamp-=step
	#+=1;
	#print ("Commited to DB for time", i)
