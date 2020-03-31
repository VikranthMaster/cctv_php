from shared import *

def writeListToFile(list, file):
    f = open(file, "w")
    for elem in list:
        f.write(elem+"\n")
    f.close()

def fix_dir(dir):
    print("Fixing directory: "+dir)
    try:
        persons = getPersonImages(dir)
    except:
        print("Exceptin in getPerson")
        return
    others = getOtherImages(dir)

    print(len(persons))
    print(len(others))

    fix_other = []
    for ot in others:
        if not ot in persons:
            fix_other.append(ot)
    print(len(fix_other))

    if len(fix_other) == len(others):
        print("Nothing to fix")
        return

    print("Fixing the others file")

    other_txt = os.path.join(dir,"others.txt")
    os.remove(other_txt)
    writeListToFile(fix_other,other_txt)


def fix_on_date(date_dir):
    for sub in get_sub_dirs(date_dir):
        fix_dir(os.path.join(date_dir,sub))

fix_date = '/mnt/hdd/GatePhotos/2019-09-04'
fix_on_date(fix_date)
