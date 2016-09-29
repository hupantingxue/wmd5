#!/usr/bin/env

import pymongo
import json
import hashlib
import conf

MAX_LINES = 500000
conn = pymongo.MongoClient("mongodb://127.0.0.1:27011")

NSTR = conf.NSTR

if "__main__" == __name__:
    """Create collections"""
    db = conn.wdb32
    for item in NSTR:
        try:
            pymongo.collection.Collection(db, item, create=True)
            print "create collection ", item
        except Exception as e:
            print "collection ", item, " already exists"
    db = conn.wdb16
    for item in NSTR:
        try:
            pymongo.collection.Collection(db, item, create=True)
            print "create collection ", item
        except Exception as e:
            print "collection ", item, " already exists"
    db = conn.wdb40
    for item in NSTR:
        try:
            pymongo.collection.Collection(db, item, create=True)
            print "create collection ", item
        except Exception as e:
            print "collection ", item, " already exists"
    db = conn.wdb64
    for item in NSTR:
        try:
            pymongo.collection.Collection(db, item, create=True)
            print "create collection ", item
        except Exception as e:
            print "collection ", item, " already exists"
    db = conn.wdb96
    for item in NSTR:
        try:
            pymongo.collection.Collection(db, item, create=True)
            print "create collection ", item
        except Exception as e:
            print "collection ", item, " already exists"
    db = conn.wdb128
    for item in NSTR:
        try:
            pymongo.collection.Collection(db, item, create=True)
            print "create collection ", item
        except Exception as e:
            print "collection ", item, " already exists"            
    print "Finished..."
