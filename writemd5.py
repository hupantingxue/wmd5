#!/usr/bin/env

import pymongo
import json
import hashlib
import sys
import conf
import os
from wdlib import save_data

MAX_LINES = 500000
conn = pymongo.MongoClient("mongodb://127.0.0.1:27011")

NSTR = conf.NSTR

BATFN = 'D:\\3\\1.bat'
TARGET_DIR = "F:\\yidaoru\\"

def Usage(name):
    """usage:"""
    print "Usage: %s <file_name>" % name

def get_md532(text):
    pwd = hashlib.md5(text).hexdigest()
    return pwd

def get_md516(text):
    pwd = hashlib.md5(text).hexdigest()[8:24]
    return pwd

def get_mmd5(text):
    tmp = hashlib.md5(text).hexdigest()
    pwd = hashlib.md5(tmp).hexdigest()
    return pwd

def get_sha1(text):
    pwd = hashlib.sha1(text).hexdigest()
    return pwd

def get_mmmd5(text):
    tmp = hashlib.md5(text).hexdigest()
    tmp = hashlib.md5(tmp).hexdigest()
    pwd = hashlib.md5(tmp).hexdigest()
    return pwd

def get_sqlmy(password):
    nr = 1345345333
    add = 7
    nr2 = 0x12345671

    for c in (ord(x) for x in password if x not in (' ', '\t')):
        nr^= (((nr & 63)+add)*c)+ (nr << 8) & 0xFFFFFFFF
        nr2= (nr2 + ((nr2 << 8) ^ nr)) & 0xFFFFFFFF
        add= (add + c) & 0xFFFFFFFF

    return "%08x%08x" % (nr & 0x7FFFFFFF,nr2 & 0x7FFFFFFF)

def get_sqlmy5(text):
    """
    Hash string twice with SHA1 and return uppercase hex digest, 
    prepended with an asterix. 
 
    This function is identical to the MySQL PASSWORD() function. 
    """
    pass1 = hashlib.sha1(text).digest()
    pass2 = hashlib.sha1(pass1).hexdigest()
    return pass2.upper()

def get_nltm(text):
    """
    Return ntlm
    """
    hash = hashlib.new('md4', text.encode('utf-16le')).digest()
    return binascii.hexlify(hash)

def get_sha256(text):
    pwd = hashlib.sha256(text).hexdigest()
    return pwd

def get_sha384(text):
    pwd = hashlib.sha384(text).hexdigest()
    return pwd

def get_sha512(text):
    pwd = hashlib.sha512(text).hexdigest()
    return pwd

def get_msha1(text):
    """
    md5(sha1(text))
    """
    tmp = hashlib.sha1(text).hexdigest()
    pwd = hashlib.md5(tmp).hexdigest()
    return pwd

def get_sha1md5(text):
    """
    sha1(md5(text))
    """
    tmp = hashlib.md5(text).hexdigest()
    pwd = hashlib.sha1(tmp).hexdigest()
    return pwd

def get_ssha1(text):
    """
    sha1(sha1(text))
    """
    tmp = hashlib.sha1(text).hexdigest()
    pwd = hashlib.sha1(tmp).hexdigest()
    return pwd

if "__main__" == __name__:
    """Save all text into files;"""
    
    mvf = open(BATFN, 'a+')
    db = conn.md5
    #COLS = [db.a, db.b, db.c, db.d, db.e, db.f, db.g, db.h, db.i, db.j, db.k, db.l, db.m, db.n, db.o, db.p, db.q, db.r, db.s, db.t, db.u, db.v, db.w, db.x, db.y, db.z, db.0, db.1, db.2, db.3, db.4, db.5, db.6, db.7, db.8, db.9]

    logfn = "run.log"
    logf = open(logfn, "a+")
    #read data from file
    #if 2 > len(sys.argv):
    #    Usage(sys.argv[0])
    #    sys.exit(-1)

    #get configure files
    types = []
    if 1 == conf.MD5_32:
        types.append("MD5_32")
    if 1 == conf.MD5_16:
        types.append("MD5_16")
    if 1 == conf.MMD5_16:
        types.append("MMD5_16")     
    if 1 == conf.MMD5:
        types.append("MMD5")
    if 1 == conf.SHA1:
        types.append("SHA1")
    if 1 == conf.MMMD5:
        types.append("MMMD5")
    if 1 == conf.SQLMY:
        types.append("SQLMY")
    if 1 == conf.SQLMY5:
        types.append("SQLMY5")
    if 1 == conf.NTLM:
        types.append("NTLM")
    if 1 == conf.SHA256:
        types.append("SHA256")
    if 1 == conf.SHA384:
        types.append("SHA384")
    if 1 == conf.SHA512:
        types.append("SHA512")
    if 1 == conf.MSHA1:
        types.append("MSHA1")
    if 1 == conf.SHA1MD5:
        types.append("SHA1MD5")
    if 1 == conf.SSHA1:
        types.append("SSHA1")
    
    #fn = sys.argv[1]
    # Init mongodb
    rootDir = "F:\\1\\"
    #dbs = {"MD5_32":conn.md5_32, "MD5_16":conn.md5_16, "MMD5_32":conn.mmd5, "SHA1":conn.sha1}
    for lists in os.listdir(rootDir):
        fn = os.path.join(rootDir, lists) 
        ii = 0
        with open(fn, 'a+') as f:
            for line in f:
                line = line.strip('\n')
                line = line.replace(' ', '')
                if not line:
                	continue
                	
                pwd = ''
                for item in types:
                    try:
                        if "MD5_32" == item:
                            pwd = get_md532(line)
                            info = """{"text":"%s", "passwd":"%s"}""" % (line, pwd)
                            type = pwd[:3]
                            info = json.loads(info)
                            #print "begin to save type: ", type
                            save_data(conn.md5_32, type, info)
                            #print "end to save", info
                        elif "MD5_16" == item:
                            pwd = get_md516(line)
                            info = """{"text":"%s", "passwd":"%s"}""" % (line, pwd)
                            type = pwd[:3]
                            info = json.loads(info)
                            save_data(conn.md5_16, type, info)
                        elif "MMD5_16" == item:
                            pwd = get_md516(get_md516(line))
                            info = """{"text":"%s", "passwd":"%s"}""" % (line, pwd)
                            type = pwd[:3]
                            info = json.loads(info)
                            save_data(conn.md5_16, type, info)
                        elif "MMD5" == item:
                            pwd = get_mmd5(line)
                            info = """{"text":"%s", "passwd":"%s"}""" % (line, pwd)
                            type = pwd[:3]
                            info = json.loads(info)
                            save_data(conn.mmd5, type, info)
                        elif "SHA1" == item:
                            pwd = get_sha1(line)
                            info = """{"text":"%s", "passwd":"%s"}""" % (line, pwd)
                            type = pwd[:3]
                            info = json.loads(info)
                            save_data(conn.sha1, type, info)
                        elif "MMMD5" == item:
                            pwd = get_mmmd5(line)
                            info = """{"text":"%s", "passwd":"%s"}""" % (line, pwd)
                            type = pwd[:3]
                            info = json.loads(info)
                            save_data(conn.mmmd5, type, info)
                        elif "SQLMY" == item:
                            pwd = get_sqlmy(line)
                            info = """{"text":"%s", "passwd":"%s"}""" % (line, pwd)
                            type = pwd[:3]
                            info = json.loads(info)
                            save_data(conn.sqlmy, type, info)
                        elif "SQLMY5" == item:
                            pwd = get_sqlmy5(line)
                            info = """{"text":"%s", "passwd":"%s"}""" % (line, pwd)
                            type = pwd[:3]
                            info = json.loads(info)
                            save_data(conn.sqlmy5, type, info)
                        elif "NTLM" == item:
                            pwd = get_ntlm(line)
                            info = """{"text":"%s", "passwd":"%s"}""" % (line, pwd)
                            type = pwd[:3]
                            info = json.loads(info)
                            save_data(conn.ntlm, type, info)
                        elif "SHA256" == item:
                            pwd = get_sha256(line)
                            info = """{"text":"%s", "passwd":"%s"}""" % (line, pwd)
                            type = pwd[:3]
                            info = json.loads(info)
                            save_data(conn.sha256, type, info)
                        elif "SHA384" == item:
                            pwd = get_sha384(line)
                            info = """{"text":"%s", "passwd":"%s"}""" % (line, pwd)
                            type = pwd[:3]
                            info = json.loads(info)
                            save_data(conn.sha384, type, info)
                        elif "SHA512" == item:
                            pwd = get_sha512(line)
                            info = """{"text":"%s", "passwd":"%s"}""" % (line, pwd)
                            type = pwd[:3]
                            info = json.loads(info)
                            save_data(conn.sha512, type, info)
                        elif "MSHA1" == item:
                            pwd = get_msha1(line)
                            info = """{"text":"%s", "passwd":"%s"}""" % (line, pwd)
                            type = pwd[:3]
                            info = json.loads(info)
                            save_data(conn.msha1, type, info)
                        elif "SHA1MD5" == item:
                            pwd = get_sha1md5(line)
                            info = """{"text":"%s", "passwd":"%s"}""" % (line, pwd)
                            type = pwd[:3]
                            info = json.loads(info)
                            save_data(conn.sha1md5, type, info)
                        elif "SSHA1" == item:
                            pwd = get_ssha1(line)
                            info = """{"text":"%s", "passwd":"%s"}""" % (line, pwd)
                            type = pwd[:3]
                            info = json.loads(info)
                            save_data(conn.ssha1, type, info)
                        else:
                            pass
                    except Exception as e:
                        continue
                    ii = ii + 1
                    if 3 == ii % 179:
                        #print "\t"
                        sys.stdout.write("\r%s %d loaded." % (fn, ii))
                        sys.stdout.flush()
                        #print "%s write %r finished" % (line, info)
        mvss = "mv %s %s\n" %(fn, TARGET_DIR)
        mvf.write(mvss)
        
        logf.write("Load  %s fininshed!\r\n" % (fn))
        logf.flush()
       
    logf.close()
    mvf.close()
    print "Finished..."