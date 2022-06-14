import shutil
import os
os.environ["TF_CPP_MIN_LOG_LEVEL"] = "2"

import tensorflow as tf
from tensorflow import keras
from tensorflow.keras import layers
import numpy as np
import matplotlib.pyplot as plt

# import numpy
# from tensorflow.keras import applications
# from tensorflow.keras.preprocessing.image import ImageDataGenerator
# from tensorflow.keras import optimizers
# from tensorflow.keras.models import Sequential, Model
# from tensorflow.keras.layers import Dropout, Flatten, Dense, GlobalAveragePooling2D
# from tensorflow.keras import backend as k
# from tensorflow.keras.callbacks import ModelCheckpoint, LearningRateScheduler, TensorBoard, EarlyStopping

print(tf.__version__)  # check tensorflow version

files_train = 0
files_validation = 0

cwd = os.getcwd()
folder = 'train_data/train'
for sub_folder in os.listdir(folder):
    if sub_folder == ".DS_Store":
        continue
    path, dirs, files = next(os.walk(os.path.join(folder, sub_folder)))
    files_train += len(files)


folder = 'train_data/test'
for sub_folder in os.listdir(folder):
    if sub_folder == ".DS_Store":
        continue
    path, dirs, files = next(os.walk(os.path.join(folder, sub_folder)))
    files_validation += len(files)

print(files_train, files_validation)

# width = int(input('Input image width: '))
# height = int(input('Input image height: '))

# was 48; change the way its cut by making it a parameter and asking the user for input
# img_width, img_height = 48, 48
img_width, img_height = 575, 280
train_data_dir = 'train_data/train'
validation_data_dir = 'train_data/test'
nb_train_samples = files_train
nb_validation_samples = files_validation
batch_size = 20  # 10 #15 #32
epochs = 20  # 15 -- original
num_classes = 2  # can modify if you want to recognize more objects

# model
model = keras.applications.VGG16(
    weights="imagenet", include_top=False, input_shape=(img_width, img_height, 3))
# Freeze the layers which you don't want to train. Here I am freezing the first 5 layers.
for layer in model.layers[:17]:
    layer.trainable = False

x = model.output  # gets layer all the way at the end
x = layers.Flatten()(x)  # add a new layer to the end
# x = layers.Dense(512, activation="relu")(x)
# x = layers.Dropout(0.5)(x)
# x = layers.Dense(128, activation="relu")(x)
# x = layers.Dropout(0.5)(x)
predictions = layers.Dense(num_classes, activation="softmax")(x)

# creating the final model
model_final = keras.Model(inputs=model.input, outputs=predictions)

# count = 0
# for layer in model.layers[:]:
#     count+=1
#     if(layer.name == 'block5_conv2'):
#         break
# print(count)

model_final.summary()

# exit()

# compile the model
model_final.compile(loss="categorical_crossentropy",
                    # can change momentum and lr
                    optimizer=keras.optimizers.SGD(lr=0.0001, momentum=0.9),
                    # optimizer=keras.optimizers.Adam(0.001),
                    metrics=["accuracy"])  # See learning rate is very low

# Initiate the train and test generators with data Augumentation
train_datagen = keras.preprocessing.image.ImageDataGenerator(
    rescale=1./255,
    horizontal_flip=True,
    fill_mode="nearest",
    zoom_range=0.1,
    width_shift_range=0.1,
    height_shift_range=0.1,
    rotation_range=5)

test_datagen = keras.preprocessing.image.ImageDataGenerator(
    rescale=1./255,
    horizontal_flip=True,
    fill_mode="nearest",
    zoom_range=0.1,
    width_shift_range=0.1,
    height_shift_range=0.1,
    rotation_range=5)

train_generator = train_datagen.flow_from_directory(
    train_data_dir,
    target_size=(img_height, img_width),
    batch_size=batch_size,
    class_mode="categorical")

validation_generator = test_datagen.flow_from_directory(
    validation_data_dir,
    target_size=(img_height, img_width),
    batch_size=batch_size, # add chen
    class_mode="categorical")

# Save the model according to the conditions
checkpoint = keras.callbacks.ModelCheckpoint(
    "weather1.h5", monitor='val_accuracy', verbose=1, save_best_only=True, save_weights_only=False, mode='auto', period=1, save_freq="epoch")
early = keras.callbacks.EarlyStopping(
    monitor='val_accuracy', min_delta=0, patience=10, verbose=1, mode='auto')

class MyThresholdCallback(tf.keras.callbacks.Callback):
    def __init__(self, threshold):
        super(MyThresholdCallback, self).__init__()
        self.threshold = threshold

    def on_epoch_end(self, epoch, logs=None): 
        val_acc = logs["val_accuracy"]
        if val_acc >= self.threshold:
            self.model.stop_training = True

my_callback = MyThresholdCallback(threshold=0.9)
# changed from 'val_acc'

# Start training!

#predictions1 = model_final.predict(validation_generator)

# original fit_generator
history_object = model_final.fit_generator(
    train_generator,
    steps_per_epoch=(nb_train_samples / batch_size),
    epochs=epochs,
    validation_data=validation_generator,
    validation_steps=(nb_validation_samples / batch_size))

predictions = model_final.predict(validation_generator)

# ,
# verbose=1,
#steps = 15,
# callbacks = [checkpoint, early])

# get incorrect predictions
predictions_new = []
predictions_filenames_no = []
predictions_filenames_yes = []

for i in predictions:
    if i[0] > i[1]:
        predictions_new.append(0)
    else:
        predictions_new.append(1)

predictions_new = np.array(predictions_new, np.float64)
predictions_new = predictions_new.transpose().astype(int)  # convert elements to integers
# print(predictions_new.size)
# print(predictions_new)


# predictions which were misclassified -- copy into new folder

# fnames is all the filenames/samples used in testing
fnames = validation_generator.filenames
errors = np.where(predictions_new != validation_generator.classes)[0]  # misclassifications of predictions
for i in errors:
    # print(fnames[i])
    if fnames[i].find("no_double_ITCZ") != -1:
        predictions_filenames_no.append(fnames[i])
        # incorrect prediction image path
        src_img = "train_data/test/" + fnames[i]
        dst_dir = "incorrect_DITCZ/" + fnames[i]  # new folder
        # copy incorrect prediction filename into new folder
        shutil.copyfile(src_img, dst_dir)
    else:
        predictions_filenames_yes.append(fnames[i])
        # incorrect prediction image path
        src_img = "train_data/test/" + fnames[i]
        dst_dir = "incorrect_DITCZ_yes/" + fnames[i]  # new folder
        # copy incorrect prediction filename into new folder
        shutil.copyfile(src_img, dst_dir)


print("\nValidation_generator.classs: ")  # print all image classifications
print(validation_generator.classes)
# print("\nPredictions_filenames: ")
# print(predictions_filenames)


print(history_object.history.keys())
plt.plot(history_object.history['accuracy'])  # changed from 'acc'
plt.plot(history_object.history['val_accuracy'])  # changed from 'val_acc'
plt.title('model accuracy')
plt.ylabel('accuracy')
plt.xlabel('epoch')
plt.legend(['train', 'test'], loc='upper left')
plt.show()

plt.plot(history_object.history['loss'])
plt.plot(history_object.history['val_loss'])
plt.title('model loss')
plt.ylabel('loss')
plt.xlabel('epoch')
plt.legend(['train', 'test'], loc='upper left')
plt.show()
