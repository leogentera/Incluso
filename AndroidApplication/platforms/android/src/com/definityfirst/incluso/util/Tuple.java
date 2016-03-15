package com.definityfirst.incluso.util;

/**
 * Created by humberto.castaneda on 3/2/2016.
 */
import java.util.Objects;
public class Tuple<X, Y> extends Object {

    public final X key;
    public final Y value;

    public Tuple(X key, Y value) {
        this.key = key;
        this.value = value;
    }

    @Override
    public String toString() {
        return "(" + key + "," + value + ")";
    }

    @Override
    public boolean equals(Object other) {
        if (other == null) {
            return false;
        }
        if (other == this) {
            return true;
        }
        if (!(other instanceof Tuple)) {
            return false;
        }
        Tuple<X, Y> other_ = (Tuple<X, Y>) other;
        return Objects.equals(other_.key, this.key) && Objects.equals(other_.value, this.value);
    }

    @Override
    public int hashCode() {
        final int prime = 13;
        int result = 1;
        result = prime * result + ((key == null) ? 0 : key.hashCode());
        result = prime * result + ((value == null) ? 0 : value.hashCode());
        return result;
    }
}