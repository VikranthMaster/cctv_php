# Importing all necessary libraries
from shared import *

# Read the video from specified path
cam = cv2.VideoCapture("vid/test.mp4")

try:
    # creating a folder named data
    if not os.path.exists('data'):
        os.makedirs('data')

    # if not created then raise error
except OSError:
    print ('Error: Creating directory of data')

# frame
currentframe = 0
cnt = 0
lastframe = None

while(True):

    # reading from frame
    ret,frame = cam.read()

    if ret:
        # writing the extracted images
        if cnt==25:
            try:
                print("Difference is : " + str(mse(lastframe, frame)))
            except:
                pass

            cnt =0
            lastframe = frame
        else:
            cnt = cnt + 1
    else:
        break

# Release all space and windows once done
cam.release()
cv2.destroyAllWindows()
