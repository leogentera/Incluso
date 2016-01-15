package com.definityfirst.incluso.modules;

import com.google.gson.annotations.SerializedName;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * Created by humberto.castaneda on 1/14/2016.
 */
public class DownloadedFile {

    @SerializedName("path")
    private String path;

    @SerializedName("name")
    private String name;

    @SerializedName("downloadLink")
    private String downloadLink;

    @SerializedName("success")
    private boolean success=true;

    public String getDownloadLink() {
        return downloadLink;
    }

    public String getName() {
        return name;
    }

    public String getPath() {
        return path;
    }

    public void setSuccess(boolean success) {
        this.success = success;
    }

    public DownloadedFile(JSONObject jsonObject){
        try {
            name=jsonObject.getString("name");
            path=jsonObject.getString("path");
            downloadLink=jsonObject.getString("downloadLink");
        } catch (JSONException e) {
            e.printStackTrace();
        }
    }
}
